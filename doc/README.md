This document is a compilation of notes from contributors, [CTLT](http://ctlt.ubc.ca/) meetings and consultations with users:

Remaining Features & Todo
============================

Not listed in any particular order:

- Implement authentication and access system
    - Use Symfony tokens
    - Use ACL for data-specific access
    - Permissions manager or configuration (similar to iPeer3 permissions manager)
    - OAuth access
- New evaluation event logic
    - Within-group (existing)
    - Self-evaluation only (new)
    - Evaluate another group
        - Evaluate X other groups
- Revised evaluation template system
    - Implement creation of templates
    - Implement completion (by students/tutors) of templates
    - Consolidate 4 types (simple, rubric, survey, mixed) into a single type
    - Add new types of questions (make the system extensible for adding new types)
        - Change simple evaluation to be $X per group, not $X per member (or make pie chart visualization)
    - Add tagging to templates for easier filtering/searching
- Add new role types
    - "UBC Grader" equivalent -> able to see results, but not able to create events, etc
    - "UBC Builder" equivalent -> able to create events, but cannot see results
- Revised emailing system 
    - Rethink email templates (possibly remove the option in some contexts)
    - Create email at various stages in process (event started, due date approaching, results availability)
    - Instructors should access a per-course log to see the history of sent emails (to answer the question "Am I spamming students?"
- Help & Resources Website:
    - The UBC CIS should develop a website to encourage better use of iPeer and peer evaluation in general
    - The site could also serve as the help/support/documentation resource
- Improved event result feedback
    - Course wide dashboard indicating trends (are students getting better over time?)
    - Each event should have a dashboard with stats (completion rate, average scores, median, mode, etc)
        - Research should be done to find which metrics are useful to instructors
    - Score manipulation / generation: normalization, include/exclude self-evaluation, remove outliers (remove highest and lowest score)
    - New export type (possible multi-sheet Excel with dashboard stats, graphs and raw data)
    - Issue detection (gaming of system, group dysfunction detection, overestimating-your-own-contribution detection, profanity in comments, etc)
    - Instructors can give per event (class-wide) or per student feedback for students to see beside their results
    - Late penalty system adjustment
        - Clearer settings
        - No submission? -> Automatic zero (even if other people rated you)
    - Student result view analytics (did students check their results)?
- Implement new and missing course management features:
    - TeamMaker (should be more transparent about the process and should be implemented in PHP, not as an external program)
    - Event creation and management
    - Add course open/close dates (course gets hidden if "old")
    - Way to deal with group formation in multi-section courses where students can switch sections for a while
- Implement missing user management features:
    - Edit own profile
    - Faculties and departments
    - Merging users
    - Importing students/users
    - Granting system wide statuses

Big Picture / Long Term Ideas
===========================

- Track student over time (over various courses)
- Get TLEF funding to continue development  
- Once the majority of the code has been written, the code should be (further) modularized and decoupled, so we end up with bundles that are reusable (potentially by others) outside the iPeer context
- LTI integration (eg. edX, or iframe in Blackboard)
- Optimize Symfony for performance and scalability (possible multi-server setup)
- Look at what similar applications are doing (eg. [TEAMMATES](https://github.com/TEAMMATES/repo)) 
- Use something like "liwc" to analyze comment quality and/or mood
