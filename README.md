MailPerformance
==
Contributors: NP6

License: MIT License

This plugin allows your subscription system to post a target on your MailPerformance account.

Description
--

Major features in MailPerformance include:

* Automatically post your target on MailPerformance when they subscribe your website.
* Sending a mail to the administrator/support of your blog when a problem appends.

PS: You'll need your 'x-Key', your 'Id Field' (it must be an 'E-mail' type and a 'Unicity Criteria') to activate the plugin. (If you don't have it, you can ask to : "apiv8@np6.com")

Installation
--

Download the plugin with the file MailPerformancePlugin.zip.

Go to plugins -> Add New -> Upload Plugin -> Select a file -> Select the file 'MailPerformance.zip' -> Install Now.

Upload the MailPerformance plugin to your blog, Activate it, then enter your 'x-Key' and 'Id Field' (it must be an 'E-mail' type and a 'Unicity Criteria').

In your subscription plugin, when your user subscribes you need to call this function "MPerf_Plugin::MPerfPostTarget([email]);" (PHP) with the email. (For more help ask to : "apiv8@np6.com")

You can call this PHP function from '/wp-admin/admin-ajax.php?email=[EMAIL]' using a basic GET call.

1, 2, 3: You're done!

=== End ===

Contact
--

Contact us at : http://www.np6.co.uk/contact-request/
