# DataModeler
DataModeler is a new ORM type framework for bulding Models that can easily speak to any datastore, mainly because they don't actually speak to the data store themselves. The \DataModeler\Model class is designed in such a way that it's data store agnostic. You can save it in a SQL database, a NoSQL database, or even a caching system like memcache or APC.

This all occurs because, again, the Models are essentially blind as to where they are stored. It is up to each data store adapter to determine how to store that model. Models, however, should be intelligent in that they contain most of the logic your application requires. Remember, you want _FAT_ models and <span style="letter-spacing: 2px;">skinny</span> controllers.
