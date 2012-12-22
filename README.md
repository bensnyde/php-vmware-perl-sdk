php-vmware-perl-sdk
===================

<h3>PHP Wrapper for Vmware vSphere Perl SDK</h3>

<p>Wrapper class for easy interfacing <a href="http://www.vmware.com">VmWare vSphere</a> data into your PHP application.</p>

<p><strong>Warning:</strong> This is intended as a guide only. This is not recommended for production use.</p>

<p>@filename vmware.php<br />@author Benton Snyder<br />@link <a href="http://noumenaldesigns.com" alt="Noumenal Designs">http://noumenaldesigns.com</a></p>

<p>Tested on vSphere 4,5</p>

<h4>To do</h4>
 <ul>
  <li>Validate method paramaters</li>
  <li>Some functions are not fully implemented<li>
 </ul>

<h4>Usage</h4>

 require('vmware.php');<br />
 $vmware = new Vmware('192.168.1.10', 'root', 'password');<br />
 $ostype = $vmware->viVersion("ostype");<br />
