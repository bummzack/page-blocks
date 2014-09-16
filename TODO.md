# Page-Blocks for SilverStripe

## TODO List

 - Collect all strings and create translations (first wrap all necessary strings in `_t()` calls.
 - Check if license is compatible with the licenses of the dependencies.
 - Add more block types<sup>1</sup>? Possible blocks:
   -  Form (maybe leverage userforms module?)
   -  Google-Map
   -  Table
 - Find a solution to nicely integrate the better buttons module (*New* button in record-view has to be disabled, because we would need a class-selector there).
 - Write tests?
 - Add icons to bulk manager actions.
 - Add custom icon(s) to "Publish Page & Blocks" Button
 - Write a manual for content-editors?
 - Get composer to do what it should...

 
1) If more blocks are being added, it might be necessary to allow toggling them on/off via `_config.yml`. A simple solution to that would be to "abuse" `canCreate`. 
