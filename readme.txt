=== Plugin Name ===
Contributors: owenks
Donate link: http://bdmfst.com/
Tags: actuarial, actuary, financial mathematics, financial, mathematics, CT1
Requires at least: 3.5.1
Tested up to: 3.5.1
Stable tag: 1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Practice financial mathematics questions for the actuarial exams. Define and
calculate annuity certain, mortgage repayment schedule.

== Description ==

This plugin provides shortcodes which render forms that asks blog users to solve 
maths questions which typically appear in the CT1 actuarial exam and
the Interest Theory part of the Financial Mathematics exam.

The shortcode specifies the type of the question (e.g to calculate the amount
of a mortgage repayment) but the parameters for the question (e.g. the amount
of the loan) are randomised so that any number of questions can be generated.

Once the blog user submits the question, the plugin scores their answer and
explains the solution.

== Installation ==

1. Download, unzip and upload to your WordPress plugins directory  
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= How do I get the MathJax rendered? =

Install and activate a Wordpress plugin like Simple Mathjax

= Where is the plugin running? = 

It's running at [Financial mathematics](http://bdmfst.com/ct1/ "Financial mathematics") 

== Screenshots ==

1. Input form rendered the first time [mortgage] shortcode is rendered.

2. Feedback once a user has submitted a form to a page with [mortgage]
shortcode.


== Changelog ==

= 1.2 = 
* Formulae shown for mortgage repayment schedule.

= 1.1 =
* shortcode attributes enable question or answer form to be excluded.
* plugin options page (help page) added to admin menu.

= 1.0 =
* annuityCertain, mortgage shortcodes.


== Upgrade Notice ==

= 1.2 =
Formulae for mortgage repayment schedule shown explicitly.

= 1.1 =
Calculator form (for any parameters) can be shown below the question form, according to what attributes you use in the shortcode.

= 1.0 =
First version.

