# concrete5 project and task management #

An add-on for http://www.concrete5.org which adds basic project and task management functionality.

## Why another project management system? ##

There are already plenty of system out there, http://www.redmine.org, http://collabtive.o-dyn.de/ and http://www.projectpier.org/ just to name a few. It took us a long time till we decided to start working on this add-on! We've been using some rails based systems for a while, the functionality was great, the quality too but we were never quite happy with running rails on our servers. Lots of problems with different gem versions and some performance issues as well.

The main reason why we started working on this is the fact that we have a lot more concrete5 know-how. Using a framework we feel comfortable about gives us a few benefits, beside the fact that we don't need a lot of features like multiple trackers, workflows, subversion integration etc.

## Should I use this add-on? ##

No! It's by far not feature complete nor stable and it's going to take a while till we recommend anyone else to use it. We are just sharing this at an early stage because we are hoping that other developers are interested in this. While we might spend some time to work on your own wishes in the future, we're mostly building this system for our own needs and therefore won't integrate a lot of features we don't need.

## How does it work? ##

We are trying to use as much of concrete5 as possible. This means that there's no kind of permissions in it beside what you get from concrete5. We are having the feeling that this gives us more than we actually need without having to build a lot of new stuff.

We are also using page types to distinguish between different objects. A project is a page which uses a page type called "project", an issue underneath a project is a page with uses a page type called "issue".

If you want to play around with this package, you should probably start by creating a new project page after you've installed the add-on. You'll see some of the functionality right after you've created the new project.

Code is maintained by [mesch.ch from Switzerland](http://www.mesch.ch)