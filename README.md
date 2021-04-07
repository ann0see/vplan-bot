# Parse Units Monitor HTML and send it to Discord

This code is ancient, no longer maintained and probably full of bugs. Sorry for removing the commit history and code style (we've learned a lot since then).

We're publishing this code in good will. If you're somehow involved in Untis development and don't want this code floating around, please contact GitHub to delete this repository although we don't think there are any legal issues here.  

Although we doubt that anybody wants to use this, we're publishing this ancient code which was mainly an idea/PoC how to get content from the HTML monitor output of Untis and send specific messages to a Discord server via WebHook.

The goal was to be notified if any lessons in a specific class are substituted by somebody else, who it is,... We've been parsing the HTML page from untis, interpreted it and substituted abbreviated teacher names with real teacher names via a MySQL Database...

## Basic documentation

How to create a db connection:
* Go to config/
* Create a config.ini file
* Configure it like this:

``[Database]
  host="host (e.g. localhost)"
  username="database user"
  password="database user's password"
  dbname="database name"
``

Initially created by @LarsVomMars, @ann0see and @Superflix26
