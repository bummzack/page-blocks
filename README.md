# Page-Blocks for SilverStripe

**IMPORTANT:** This module is currently work-in-progress and is subject to major changes or refactoring. Here's the current [TODO](TODO.md).

## What is this?
Page-Blocks is a Module that adds content to a SilverStripe page in a modular way. Ever had a client that wanted some text, followed by a gallery, some more text and then a video?

You can do some of these things by allowing the user to insert videos and images directly into the content-editor (TinyMCE), but you'll lose control over the website-design.

The goal of this module is to allow flexible page-designs, while giving you full control over the design of the content. It's also great for single-page websites.

This module should be light-weight and extensible. We provide the backend integration, while **you** have to care about the frontend (we won't ship another gallery module). This isn't a all-round carefree package, but we provide some basic templates and (hopefully) a good documentation. 

## Requirements

#### Mandatory
 - [SilverStripe 3.1](http://www.silverstripe.org/stable-download/)
 - [GridField Bulk Editing Tools](https://github.com/colymba/GridFieldBulkEditingTools)
 - [GridField Extensions](https://github.com/ajshort/silverstripe-gridfieldextensions)

#### Optional (for a better experience)

 - [sortablefile](https://github.com/bummzack/sortablefile)
 - [Better Buttons for GridField](https://github.com/unclecheese/silverstripe-gridfield-betterbuttons)

## Installation

Use composer to install the module and all its dependencies.

    composer require "bummzack/page-blocks" "dev-master"
    
If you don't use composer, make sure you install at least the modules that are listed as "mandatory" under **Requirements**