# PHP libs by Murdej

## 1.8.0

 - new static class `Math`
   - `divideByRate` - Divides the number into parts according to the ratio
 - `ProcI`
   - For the `unique` method it is possible to pass a callbck that gets the value by which the uniqueness of the element is determined. Therefore it can be used on elements of array or object type. It is possible to pass a callback which of the "same" elements is the correct one.
   - For text callbacks it is possible to use the `-` prefix to change the positive/negative number.
   - `first` not required first argument

## 1.7.0

 - New method `Arrays::renameKeys`

## 1.6.0

 - New method `Arrays::getValue` and `Arrays::setValue`

## 1.5.0

 - New method `Arrays::flatten` and `Arrays::unflatten`

## 1.3.0

 - `ProcI`
   - String callbacks chaining (It is possible to use, for example `.foo.bar[1`)
   - String callbacks allows null operator (`.foo?.bar`)

## 1.2.0

 - `TreeMaker` 
   - link to parent in `TreeItem`
   - 
 - `CollectionDiff` Input collections can be iterable. (Originally only array)
 - `ProcI`
   - method `each`

## 1.0.0 First published version

