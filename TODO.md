# Page-Blocks for SilverStripe

## TODO List

 - [x] Collect all strings and create translations (first wrap all necessary strings in `_t()` calls.
 - Create language files
   - [x] English 
   - [x] German
   - ...
 - [ ] Check if license is compatible with the licenses of the dependencies.
 - [ ] Add more block types<sup>1</sup>? Possible blocks:
   -  [ ] Form (maybe leverage userforms module?)
   -  [ ] Google-Map
   -  [ ] Table
 - [ ] Find a solution to nicely integrate the better buttons module (*New* button in record-view has to be disabled, because we would need a class-selector there).
 - [ ] Write tests?
 - [ ] Add icons to bulk manager actions.
 - [ ] Add custom icon(s) to "Publish Page & Blocks" Button
 - [ ] "Publish Page & Blocks" Button appearance should also change when page *or* blocks were modified.
 - [ ] Write a manual for content-editors?
 - [x] Get composer to do what it should...
 - [ ] Make version history of blocks available?
 - [ ] Test integration with major translatable solutions (translatable and fluent)
 - [ ] Maybe a page could have several sections that contain page-blocks? Something akin to Widget-Areas that contain Widgets.
 - [ ] Get rid of the `has_many` relation to make blocks reusable (eg. `many_many`)?

 
1) If more blocks are being added, it might be necessary to allow toggling them on/off via `_config.yml`. A simple solution to that would be to "abuse" `canCreate`. 
