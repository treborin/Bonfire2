# Change Log

This holds the change history for Bonfire as we lead up to a 1.0 release. It's not exhaustive, but should give you a good idea of the changes that have been made and how it might impact you.

IMPORTANT! *Breaking changes* are marked with words `breaking change` in parentheses right after the date.

## 16 January 2024

Bonfire can henceforth warn you about breaking changes (like need to update Admin/Auth themes, change config files, etc).

To get such notifications when updating Bonfire, you can add a command to your
composer.json scripts section:

```json
    "scripts": {
        "post-update-cmd": [
            "php spark notify:breaking-changes"
        ]
    }
```

New Bonfire install will automatically include this command.

## 18 March 2024 (breaking change)

**<x-button\>** and **<x-button-container\>** view [components](https://github.com/lonnieezell/Bonfire2/blob/develop/docs/building_admin_modules/view_components.md) implemented.

When updating, copy files `themes/Admin/Components/button-container.php` and `themes/Admin/Components/button.php` into the corresponding folder of your project's Admin theme. See [#434](https://github.com/lonnieezell/Bonfire2/pull/434) for details about the change.
