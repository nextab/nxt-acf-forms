# nxt-acf-forms

Integrating Contact Forms on a WordPress site with the help of the Advanced Custom Fields (ACF) plugin

# The plugin does the following things

- Create a custom post type `messages`
- Use a shortcode to create a frontend ACF form
- Create a `message` with the help of a frontend form (similar to a contact form)
- Send an e-mail to all administrators of the website whenever a post of the custom post type is being created on the frontend (changes to the post on the backend will not trigger a swarm of e-mail notifications)
- Filter and sort all messages in the backend with additional admin columns

## Features

- The plugin automatically pulls all custom fields associated with a custom post type and lists them in the form
- Compatible with the taxonomy field type that allows better data organization in the backend
- Makes use of the ACF honeypot functionality to prevent spam bots from flooding your inbox
- Shortcode allows you to use forms anywhere in your WordPress site; no need to (heavily) customize page templates, etc.
- Supports internationalization; the plugin is ready to be translated

## Issues/Feature requests

- After filtering columns in the backend (e.g. clicking on an e-mail to see all e-mails that this particular customer has sent), the sorting algorithm doesn't work (it does if you haven't applied a filter first)
- The `acf_form_head()` function still needs to be present in the template; it would be nice to have some logic that detects that the shortcode is present on a page and it then integrates the function automatically
- Define a logic to add new columns in the admin backend automatically (at the moment, certain fields can't be renamed as this would break the logic for the admin column extension part of the plugin)

# Installation

- Activate Advanced Custom Fields (if you haven't already)
- Upload the plugin to your WordPress plugin directory and activate it
- Import the `.json` file from the `form export` folder to ACF
- Create a page template that integrates `acf_form_head()` at the top of the page
- Create a page with the new page template and use the shortcode `[nxt_cf]` on the page

You're done; you can start styling the contact form. If someone's using the form on the frontend, a new message will be created in the backend and all admin accounts will receive an e-mail notification.
