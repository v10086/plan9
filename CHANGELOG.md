# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased]
- Remove `app/library` directory (deleted library implementations).
- Rename internal symbols for readability (non-breaking):
  - `WebSocketClient` internal private members and methods renamed for consistency.
  - `RedisQueue` added `getQueues()` and retained `queues()` for compatibility.
- Update `README.md` to remove library usage docs and keep a minimal quick-start.

## 2025-12-08
- Project cleanup: removed unused library folder and simplified documentation.

