# iPeer v4 Status Report: April 2015

*Written by @gondek.*

This report is primarily targeted at future [Centre for Instructional Support](http://cis.apsc.ubc.ca/) co-op students who would want to contribute to this project, but it should contain useful information for other contributors as well.

## Introduction

iPeer is an open source peer evaluation tool developed by UBC. It was originally created at and hosted by the [Centre for Instructional Support](http://cis.apsc.ubc.ca/) to reduce the difficulty of administering and analyzing paper based evaluations. Project ownership was transferred over to [CTLT](http://ctlt.ubc.ca/) when usage grew outside of the Faculty of Applied Science and UBC. CTLT maintains UBC's running version at [https://ipeer.elearning.ubc.ca/](https://ipeer.elearning.ubc.ca/).

iPeer 3 is the current version. It runs on a LAMP setup (Linux, Apache, [MySQL](https://www.mysql.com/) and [PHP](http://php.net/)) and uses [CakePHP 2](http://cakephp.org/) as a web application framework. On the front-end, is uses [JQuery](http://jquery.com/) and [Prototype](http://prototypejs.org/) to support the HTML/CSS.

iPeer 4 was started when we wanted to add new features that would strongly conflict with the existing design (i.e. they could not be added without triggering the equivalent of a complete rewrite). In addition, iPeer's user interface was in need of an update and the backend framework code was getting old.

Feature requests came from the iPeer 3 issue tracker, consultations with the Sauder School of Business and consultations with the Faculty of Applied Science. Once development ramps up, more focus groups should be held with instructors, students and instructional support staff.

The people at CTLT who have been involved in iPeer are Pan Luo ([@xcompass](https://github.com/xcompass)), Letitia Englund ([@lenglund](https://github.com/lenglund)), Michael Tang ([@mwytang](https://github.com/mwytang)) and John Hsu ([@ionparticle](https://github.com/ionparticle)). They can be consulted for more information about the project.

## Where to start

If new to iPeer, first familiarize yourself with iPeer 3. Then read the main feature changes which are outlined in this report and the repository's documentation folder (`/doc/README.md`). Before working on them, try addressing the todos for the existing iPeer 4 features before implementing new ones. Arranging a meeting with CTLT to plan out changes and goals would also be a good idea.

If new to web application programming, the [Symfony Book](http://symfony.com/doc/current/book/index.html) ([PDF version](http://symfony.com/pdf/Symfony_book_2.6.pdf)) provides a great explanation of the rationale behind using MVC (model-view-controller), a front-controller (and other routing conventions), and other framework patterns. However, since we are using Symfony for API construction and data management, the sections on views, templates and Twig are not relevant.

For the other technologies involved and some suggested resources, refer to the root [`README.md`](../README.md).

Some suggested starting places are:

- **Testing Deletes**: Tests checking whether connected entities get deleted when a dependency of theirs gets deleted need to be written (I am not sure if this happens). For example, deleting a course should delete the groups and enrollments in that course.
- **Cleaning up tests**: Many test cases/methods try to do too much. They should be split into more specific cases. Reading over them will also give you a sense of how the API works.
- **API Design**: The API currently supports all the operations (create, read, update, delete) for the completed entities (users, enrollments, faculties, etc), but only at a basic level. First of all, the ability to sort, search, filter and paginate resources through the use of query parameters should be implemented (eg. `/users?limit=50&page=2`). Secondly, research should be done on best practices when sending update requests, especially multiple creations/updates in a single request. The API endpoints/URLs will likely need to be changed as a result.
- **Working on the front-end**: Continuing work on the front end would be a good way to discover how the API works and where there are areas for improvement. More specific todo items for the front end are listed in its repository (the link may change when ownership changes): https://github.com/tkbaylis/ipeer-client-test

More details on these and other items are in the todo list (`/doc/README.md`).

## Design Choices

We are continuing to use PHP and MySQL (although Doctrine's DBAL allows users to use a different database) because we want migrations for current users of v3 to be easy (in terms of server setup). In addition, PHP is still one of the wider supported languages on hosting platforms.

iPeer 4 uses the Symfony web application framework. Other frameworks, like [Silex](http://silex.sensiolabs.org/), [Laravel](http://laravel.com/), and [Slim](http://www.slimframework.com/) were considered, but Symfony was chosen on recommendation from CTLT and due to its:

- **Modularity**: There is more flexibility in choosing which components to use, which is nice to have since an API would not use as many as a regular web application.
- **Existing REST/API Support**: Bundles to help API construction were easily available.
- **Database Abilities**: We really liked [Doctrine](http://www.doctrine-project.org/projects/orm.html)'s style of database manipulation and Symfony makes it easy to use (other frameworks would require more configuration and "middleware").
- **Maturity**: I am not able to judge this for myself, but CTLT and online sources indicate that Symfony has a history of keeping up to date with new PHP conventions and has a large community that supports that goal.

An API approach was taken to encourage decoupling of the front-end from the back-end and to open up the possibility of other clients accessing iPeer (at the very least some learning management systems). The API's default format is JSON, but at a later date XML could be added through configuring the REST Bundle and adding some XML schema annotations to the entities.

## What's Done So Far

- **Bundle set up**: The recommended bundles for building an API have been installed and configured.
- **Models**: The definitions and relationships between these entities have been created: Courses, Student/Tutor Groups (within courses), Departments, Faculties, Enrollments (within courses: {instructor, tutor, student}), and Users. In addition, serialization/deserialization annotations were added.
- **Controllers**: Controllers for CRUD (create, read, update, & delete) operations on the entities have been completed. The routes and controllers take into account the ownership relation between some types of entities (eg. since groups belong to courses, they are accessed at `/api/courses/{course}/groups` instead of `/api/groups`).
- **Validation**: Some field-based validation has been implemented through the use of [annotations](http://symfony.com/doc/current/book/validation.html). Any time a body parameter converter (`@ParamConverter("<modelName>", converter="fos_rest.request_body")`) is used, validation gets run during deserialization. Any validation errors are caught by a custom listener (`ValidationErrorListener.php`) and get turned into a HTTP 400 response.
- **Basic test setup & fixtures**: Fixtures containing sample data were constructed and a base `IpeerTestCase.php` was created with an utility method for checking if responses return the expected HTTP code and `content-type`.

## Key Changes (upcoming)

The following tasks will likely take up the majority of the remaining development time:

- **Authentication**: This includes managing logins and validating data. Since an API should be stateless, logins should be handled through an API key generated upon the initial user credential check. [This](http://symfony.com/doc/current/cookbook/security/api_key_authentication.html) Symfony Cookbook article could be a starting point. Completing this will also trigger a rewrite of the tests since all of them are written from a super-admin perspective (tests will need to be added from the perspective of an admin, instructor, tutor and student).
- **Validation**: All actions and input data should be validated. For example, students should only be able to be added to groups if they are in the course that the group belongs to. For the current entities, this should be already complete, but as the system grows in features, managing this kind of logic will become more complex. One particular case will be submitting evaluations (eg. is the user allowed to submit an evaluation for this person?). Validation also relates to authentication (authorization). For example, can the user perform this action (creating a user), can the user access this course (are they an instructor/student in that course)?
- **Event logic**: iPeer 3 only supports evaluations within groups (team members evaluate each other). iPeer 4 should support additional event logic like: all the members of one group evaluate another group, each student evaluates each group, each student does a self-evaluation, etc. The logic of who is giving and receiving evaluations should be abstracted in a way that allows for new types of evaluations to be added.
- **Revised evaluation template system**: The way templates are created and managed can be improved. See the todo file for specific points.
- **Improved event result feedback and class status**: While we will always want to retain a direct data export for custom analysis, more feedback should be given to the instructor during various phases of the events (before: reminder emails have been sent out, during: x% of students have completed it, after: yet to be determined metrics).
- **User interface**: See the front-end client repository for more notes on this.
