<?php

namespace library;

class WebSocketClient
{
        /** @var string */
        private $host;
        /** @var int */
        private $port;
        /** @var string */
        private $path;
        /** @var string|false */
        private $origin;
        /** @var resource|null */
        private $socket = null;
        /** @var bool */
        private $connected = false;

        public function __construct()
        {
        }

        public function __destruct()
        {
                $this->disconnect();
        }

        // 发送数据（保留原 public API 名称）
        public function sendData($data, $type = 'text', $masked = true)
        {
                if ($this->connected === false) {
                        trigger_error("Not connected", E_USER_WARNING);
                        return false;
                }

                if (!is_string($data)) {
                        trigger_error("Not a string data was given.", E_USER_WARNING);
                        return false;
                }
                if (strlen($data) == 0) {
                        return false;
                }
                $res = @fwrite($this->socket, $this->hybi10Encode($data, $type, $masked));

                if ($res === 0 || $res === false) {
                        return false;
                }
                $buffer = '';
                return true;
        }

        public function connect($host, $port, $path, $origin = false)
        {
                $this->host = $host;
                $this->port = $port;
                $this->path = $path;
                $this->origin = $origin;

                $key = base64_encode($this->generateRandomString(16, false, true));
                $header = "GET " . $path . " HTTP/1.1\r\n";
                $header .= "Host: " . $host . ":" . $port . "\r\n";
                $header .= "Upgrade: websocket\r\n";
                $header .= "Connection: Upgrade\r\n";
                $header .= "Sec-WebSocket-Key: " . $key . "\r\n";

                if ($origin !== false) {
                        $header .= "Sec-WebSocket-Origin: " . $origin . "\r\n";
                }
                $header .= "Sec-WebSocket-Version: 13\r\n\r\n";

                $this->socket = fsockopen($host, $port, $errno, $errstr, 2);
                socket_set_timeout($this->socket, 2, 10000);
                $res = @fwrite($this->socket, $header);
                if ($res === false) {
                        echo "fwrite false \n";
                }

                $response = @fread($this->socket, 1500);
                preg_match('#Sec-WebSocket-Accept:\s(.*)$#mU', $response, $matches);
                if ($matches) {
                        $keyAccept = trim($matches[1]);
                        $expectedResonse = base64_encode(pack('H*', sha1($key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
                        $this->connected = ($keyAccept === $expectedResonse) ? true : false;
                }
                return $this->connected;
        }

        public function checkConnection()
        {
                $this->connected = false;

                // send ping:
                $data = 'ping?';
                @fwrite($this->socket, $this->hybi10Encode($data, 'ping', true));
                $response = @fread($this->socket, 300);
                if (empty($response)) {
                        return false;
                }
                $response = $this->hybi10Decode($response);
                if (!is_array($response)) {
                        return false;
                }
                if (!isset($response['type']) || $response['type'] !== 'pong') {
                        return false;
                }
                $this->connected = true;
                return true;
        }

        public function disconnect()
        {
                $this->connected = false;
                is_resource($this->socket) and fclose($this->socket);
        }

        public function reconnect()
        {
                sleep(10);
                $this->connected = false;
                fclose($this->socket);
                $this->connect($this->host, $this->port, $this->path, $this->origin);
        }

        // 私有：生成随机字符串（去掉前导下划线，仍为私有方法）
        private function generateRandomString($length = 10, $addSpaces = true, $addNumbers = true)
        {
                $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!"ยง$%&/()=[]{}';
                $useChars = array();
                for ($i = 0; $i < $length; $i++) {
                        $useChars[] = $characters[mt_rand(0, strlen($characters) - 1)];
                }
                if ($addSpaces === true) {
                        array_push($useChars, ' ', ' ', ' ', ' ', ' ', ' ');
                }
                if ($addNumbers === true) {
                        array_push($useChars, rand(0, 9), rand(0, 9), rand(0, 9));
                }
                shuffle($useChars);
                $randomString = trim(implode('', $useChars));
                $randomString = substr($randomString, 0, $length);
                return $randomString;
        }

        // 私有：编码帧（去掉前导下划线）
        private function hybi10Encode($payload, $type = 'text', $masked = true)
        {
                $frameHead = array();
                $frame = '';
                $payloadLength = strlen($payload);

                switch ($type) {
                        case 'text':
                                $frameHead[0] = 129;
                                break;
                        case 'close':
                                $frameHead[0] = 136;
                                break;
                        case 'ping':
                                $frameHead[0] = 137;
                                break;
                        case 'pong':
                                $frameHead[0] = 138;
                                break;
                }

                if ($payloadLength > 65535) {
                        $payloadLengthBin = str_split(sprintf('%064b', $payloadLength), 8);
                        $frameHead[1] = ($masked === true) ? 255 : 127;
                        for ($i = 0; $i < 8; $i++) {
                                $frameHead[$i + 2] = bindec($payloadLengthBin[$i]);
                        }
                        if ($frameHead[2] > 127) {
                                $this->close(1004);
                                return false;
                        }
                } elseif ($payloadLength > 125) {
                        $payloadLengthBin = str_split(sprintf('%016b', $payloadLength), 8);
                        $frameHead[1] = ($masked === true) ? 254 : 126;
                        $frameHead[2] = bindec($payloadLengthBin[0]);
                        $frameHead[3] = bindec($payloadLengthBin[1]);
                } else {
                        $frameHead[1] = ($masked === true) ? $payloadLength + 128 : $payloadLength;
                }

                foreach (array_keys($frameHead) as $i) {
                        $frameHead[$i] = chr($frameHead[$i]);
                }
                if ($masked === true) {
                        $mask = array();
                        for ($i = 0; $i < 4; $i++) {
                                $mask[$i] = chr(rand(0, 255));
                        }

                        $frameHead = array_merge($frameHead, $mask);
                }
                $frame = implode('', $frameHead);

                for ($i = 0; $i < $payloadLength; $i++) {
                        $frame .= ($masked === true) ? $payload[$i] ^ $mask[$i % 4] : $payload[$i];
                }

                return $frame;
        }

        // 私有：解码帧（去掉前导下划线）
        private function hybi10Decode($data)
        {
                $payloadLength = '';
                $mask = '';
                $unmaskedPayload = '';
                $decodedData = array();

                $firstByteBinary = sprintf('%08b', ord($data[0]));
                $secondByteBinary = sprintf('%08b', ord($data[1]));
                $opcode = bindec(substr($firstByteBinary, 4, 4));
                $isMasked = ($secondByteBinary[0] == '1') ? true : false;
                $payloadLength = ord($data[1]) & 127;

                switch ($opcode) {
                        case 1:
                                $decodedData['type'] = 'text';
                                break;
                        case 2:
                                $decodedData['type'] = 'binary';
                                break;
                        case 8:
                                $decodedData['type'] = 'close';
                                break;
                        case 9:
                                $decodedData['type'] = 'ping';
                                break;
                        case 10:
                                $decodedData['type'] = 'pong';
                                break;
                        default:
                                return false;
                                break;
                }

                if ($payloadLength === 126) {
                        $mask = substr($data, 4, 4);
                        $payloadOffset = 8;
                        $dataLength = bindec(sprintf('%08b', ord($data[2])) . sprintf('%08b', ord($data[3]))) + $payloadOffset;
                } elseif ($payloadLength === 127) {
                        $mask = substr($data, 10, 4);
                        $payloadOffset = 14;
                        $tmp = '';
                        for ($i = 0; $i < 8; $i++) {
                                $tmp .= sprintf('%08b', ord($data[$i + 2]));
                        }
                        $dataLength = bindec($tmp) + $payloadOffset;
                        unset($tmp);
                } else {
                        $mask = substr($data, 2, 4);
                        $payloadOffset = 6;
                        $dataLength = $payloadLength + $payloadOffset;
                }

                if ($isMasked === true) {
                        for ($i = $payloadOffset; $i < $dataLength; $i++) {
                                $j = $i - $payloadOffset;
                                if (isset($data[$i])) {
                                        $unmaskedPayload .= $data[$i] ^ $mask[$j % 4];
                                }
                        }
                        $decodedData['payload'] = $unmaskedPayload;
                } else {
                        $payloadOffset = $payloadOffset - 4;
                        $decodedData['payload'] = substr($data, $payloadOffset);
                }

                return $decodedData;
        }
}
