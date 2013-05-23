=== Plugin Name ===
Contributors: owenks
Donate link: http://bdmfst.com/
Tags: actuarial, actuary, financial mathematics, financial, mathematics, CT1
Requires at least: 3.5.1
Tested up to: 3.5.1
Stable tag: 1.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Practice financial mathematics questions for the actuarial exams. Define and
calculate interest rates, annuity certain, mortgage repayment schedule.

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

= How do I run the unit tests? =

Install phpunit and enter
$phpunit

== Screenshots ==

1. Input form rendered the first time [mortgage] shortcode is rendered.

2. Feedback once a user has submitted a form to a page with [mortgage]
shortcode.


== Changelog ==
= 1.7 =
* Given values (e.g. mortgage instalments), calculate effective interest rate.
* Escalating and increasing annuities.

= 1.6 =
* Renamed files to conform better to Wordpress PHP coding standards.
* Added phpunit tests.

= 1.5 =
* Bug fix. Annuity "Just show me the answer" button redirects to correct page.
* Bug fix. Formula for i(m) in mortgage schedule has right sign in power.  

= 1.4 =
* Bug fix.  Shortcode attributes now work.

= 1.3 =
* convertInt shortcode added (converts interest rates).

= 1.2 = 
* Formulae shown for mortgage repayment schedule.

= 1.1 =
* shortcode attributes enable question or answer form to be excluded.
* plugin options page (help page) added to admin menu.

= 1.0 =
* annuityCertain, mortgage shortcodes.


== Upgrade Notice ==

= 1.7 =
Level, escalating, increasing annuities can be calculated (based on yield) or if you input their value you can get the yield.

= 1.5 =
* Bug fix. Annuity "Just show me the answer" button redirects to correct page.

= 1.4 =
Bug fix.  Shortcode attributes now work so that question or answer form can be
excluded.

= 1.3 = 
Interest rate conversion questions added e.g. convert from annual effective
rate to discount rate convertible monthly.

= 1.2 =
Formulae for mortgage repayment schedule shown explicitly.  Plugin's styles defined in ct1.css and added to header.

= 1.1 =
Calculator form (for any parameters) can be shown below the question form, according to what attributes you use in the shortcode.

= 1.0 =
First version.

