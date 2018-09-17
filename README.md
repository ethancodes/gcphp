# gcphp
A GatherContent library for PHP

Copy `config.exmaple.json` to `config.json` and edit it so that it has your info.

```
include 'gcphp.php';
$hiccup = new GCPHP();
```

You can load a different config file by doing

```
$hiccup = new GCPHP('config.local.json');
$otherp = new GCPHP('config.project2.json');
```

## Functions you should know and love

### Project specific functions

`getItems()` will give you all the items.

`getTemplates()` will give you all the templates.

`getWorkflowStates()` will give you all the workflow states.

### Item specific functions

`getItem($item_id)` will give you that item.

`getItemName($item)` will take the item you got from `getItem($item_id)` and return the name.

`getItemStatus($item)` does almost the same thing, returning the workflow state.

`getItemTemplate($item)` does almost the same thing, return the name of the template.

`getItemValue($item, $field_name)` gets you the value of 'Content'.

`getItemFile($item, $field_name)` gets you the URL to 'Thumbnail'. Because there may be multiple files uploaded to this field, return values will vary. If there's only one file, you'll get a string for that file's URL. If there are multiple files, you'll get an array of strings. **NOTE that in my experience the GatherContent API will give you the URL of a file even after the file has been deleted from the field.**