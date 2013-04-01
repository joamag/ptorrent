# [pTorrent Bittorrent Tracker](http://ptorrent.com)

Simple tracker for the bittorrent protocol implemented in PHP.

It's implementing using a simple local SQLite database and provides support for both
the tracking and for a simple web interface for exploration and management. It currently
supports both announcing (`anounce.php`) and scrapping (`scrape.php`).

## Installation

Download the source code into your htdocs folder and make sure the user associated with
your web server has write permissions on the database files directory (`db`) directory.
Write permission may also be required for the templates cache directory (`templates_c`).

## Usage

To be able to create torrent files and upload them to the tracker use the [mktorrent](http://mktorrent.sourceforge.net/)
utility menat to be used from a command line interface. Note that the win32 port uses cygwin
and is not possible to compile it using msvc.

## Reference
The tracker has been implemented according to the [bitorrent official specification](http://wiki.theory.org/BitTorrentSpecification).
