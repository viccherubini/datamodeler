# DataModeler
DataModeler is a new ORM type framework for bulding Models that can easily speak to any datastore, mainly because they do not actually speak to the data store themselves. The `\DataModeler\Model` class is designed in such a way that it is data store agnostic. You can save it in a SQL database, a NoSQL database, or even a caching system like memcache or APC.

This all occurs because, again, the Models are essentially blind as to where they are stored. It is up to each data store adapter to determine how to store that model. Models, however, should be intelligent in that they contain most of the logic your application requires. Remember, you want **F A T** models and skinny controllers.

Using DataModeler is simple. All of your models should extend the `\DataModeler\Model` class (which is fairly simple itself). Next, you will build a `\DataModeler\Loader` and `\DataModeler\Writer` object for loading models and writing them, respectively. A `\DataModeler\Adapater` instance is then built from an Abstract Factory class. Finally, the adapter is connected to through a consistent interface and attached to both the Loader and Writer objects. You can now load and write objects.

Of course, the order is not important initially; the Adapter can be built before the Loader and Writer objects. However, to load and write objects, an Adapater much be attached to each of the Loader and Writer.
