# [pTorrent Bittorrent Tracker](http://ptorrent.com)

Simple tracker for the bittorrent protocol implemented in PHP.

It's implementing using a simple local SQLite database and provides support for both
the tracking and for a simple web interface for exploration and management.

## Installation

Download the source code into your htdocs folder and make sure the user associated with
your web server has write permissions on the database files directory (`db`) directory.
Write permission may also be required for the templates cache directory (`templates_c`).

## Reference
Implemented according to the [bitorrent official specification](http://wiki.theory.org/BitTorrentSpecification).
