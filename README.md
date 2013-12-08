# HasOneCompositeField

This module allows you to add and edit a has_one directly from within the parent record form, made to look as if it is
part of the parent record form.

I haven't tested it with GridField yet (haven't needed to). Would like to hear if it works, but I'm 40/60 sure it won't
work as is.

## Requirements

*  SilverStripe 3.1

## Author

*  Mellisa Hankins [mell@milkywaymultimedia.com.au]

## Install using composer

```
composer require milkyway/silverstripe-hasonecompositefield:*
```

## Example Code

```
$relField = HasOneCompositeField::create('ContentBlock', 'Content Block', $this->ContentBlock(), $fields = null);
```

If no fields are defined, the field will try to find a getHasOneCMSFields method on the passed in record, otherwise it
will fall back to getCMSFields.

Note: This will save the record if it does not exist yet when the form is saved. At the moment, there is no way to delete
the record, but there are alernative modules which offer that solution if you need it.