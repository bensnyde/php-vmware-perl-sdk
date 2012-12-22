php-vmware-perl-sdk
===================

<h3>PHP Wrapper for Vmware vSphere Perl SDK</h3>

<p>Wrapper class for easy interfacing with <a href="http://www.vmware.com">Vmware vSphere</a> allowing for simple integration of vSphere functionality into your own applications.</p>

<p>@filename vmware.php<br />@author Benton Snyder<br />@link <a href="http://noumenaldesigns.com" alt="Noumenal Designs">http://noumenaldesigns.com</a></p>

<p>Tested on vSphere 4,5</p>

<h4>Usage</h4>

 require('vmware.php');<br />
 $vmware = new Vmware('192.168.1.10', 'root', 'password');<br />
 $ostype = $vmware->viVersion("ostype");<br />
