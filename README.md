#Advanced Custom Fields - Field Group Widget

#Description:
A WordPress widget used allongside the fantastic Advanced Custom Fields plugin. This widget will allow you to select from a list of your current field groups, create a folder and empty template file in your themes directory, then display the contents of this template file in your specified location. Perfect for use with the "Options" add-on for Advanced Custom Fields.

#Usage:
Install, as you would, any other WordPress widget. When the widget is first called from a page view, it will search for and try to parse a ACF Widget Template. If the template, or "acf-widgets" directory does not exist within your theme folder, the folder and a blank template will be created.

/wp-contents/[theme-folder]/acf-widgets/acf_[field_group_name]-template.php

The created template will only serve to provide the user with information on where to look for this template and what Name, Slug, and ID of the chosen Field Group are being referenced. The contents of the blank template can be deleted and filled up with all that ACF goodness!

![Alt text](/acf_field_group_widget_screenshot.png "Example Screenshot")
