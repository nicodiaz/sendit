sendit
======

PHP Project to to send mails asynchronously.

- Supports different types of mails
- Use a DB driver to enqueue the mails (soon other drivers like text files)
- Run as a cron job

Releases
========

0.9
---

- Add items to the queue (library)
- Process queue (library)
- Shell script to configure a cron job (process-queue.sh)

1.0.0
-----
- First Stable Version

1.0.1
-----
- Changes in constructor to receive the configuration params

1.0.2
-----
- The config is removed from "require_once" section of the library
