# Section 4 - WordPress PHP code to register custom post type - Case Studies

This code is used to register a custom post type for case studies and adds a shortcode to display featured entries on the frontend.

## Features

### 1. Custom Post Type

The code includes to register a custom post type named `case_study` with the following settings:

- Label: Case Studies
- Publicly accessible: yes
- Available in the REST API: yes
- Supports: title, editor, and thumbnail
- Archive enabled: yes
- Archive slug: `case-studies`

This is created with `register_post_type('case_study', ...)` and hooked into `init`.

### 2. Shortcode

The plugin adds the shortcode:

```php
[featured_case_studies]
```

This shortcode runs a `WP_Query` for the `case_study` post type and displays up to 3 published case studies, ordered by date descending. Each result shows the title and excerpt inside a `featured-case-studies` container.

If no case studies are found, it returns:

```html
<p>No case studies found.</p>
```

### 3. Archive Title Filter

A filter is added to `the_title` to modify the archive title for case study pages:

```php
if (is_post_type_archive('case_study')) {
    $title = 'Case Study: ' . $title;
}
```

This prepends `Case Study:` to the archive page title when viewing the custom post type archive.

## Usage

1. Add new case studies from the WordPress admin.
2. Insert the shortcode `[featured_case_studies]` into any page or post.
3. Visit the archive page for the case studies to see the modified archive title.
