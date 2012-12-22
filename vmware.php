<?php

/**
 * PHP Wrapper of VmWare vSphere Perl SDK
 *
 * Wrapper class for easy interfacing VmWare vSphere data into your PHP application. 
 * Not all functionality provided by vSphere or the Perl SDK is implemented. 
 * 
 * Intended as a guide only. Not recommended for production use. 
 *
 * @category   VmwareWrapper
 * @package    Vmware
 * @author     Benton Snyder <noumenaldesigns@gmail.com>
 * @copyright  2012 Noumenal Designs
 * @license    WTFPL
 * @link       http://www.noumenaldesigns.com
 */

class Vmware
{
  private $host;
	private $username;
	private $password;
	private $perl;			// path to perl
	private $sdk;			// path to vSphere Perl SDK

	/**
	 * Public constructor
	 *
	 * @access		public
	 * @param
	 * @return
	 */
	function __construct($host, $username, $password, $perl='$this->perl', $sdk='$this->sdk')
	{
		parent::__construct();
		
		$this->host = $host;
		$this->username = $username;
		$this->password = $password;
		$this->perl = $perl;
		$this->sdk = $sdk;
	}

	/**
	 * Displays the specified attributes of the specified virtual machine
	 *
	 * @access       public
	 * @param        string
	 * @return       string
	 */
	public function getVmInfo($vmname)
	{
		$details = shell_exec("{$this->perl} {$this->sdk}vm/vminfo.pl --vmname {$vmname} --server {$this->host} --username {$this->username} --password '{$this->password}'");
		return $details;
	}

	/**
	 * List the properties of the specified virtual machines
	 *
	 * @access       public
	 * @param        string
	 * @return       string
	 */
	public function getGuestInfo($vmname, $action = "display")
	{
		if($action == "display" || $action == "customize")
		{
			$details = shell_exec("{$this->perl} {$this->sdk}vm/guestinfo.pl --operation {$action} --vmname {$vmname} --server {$this->host} --username {$this->username} --password '{$this->password}'");
			return $details;
		}
	}

	/**
	 * Display or modify shares for memory, CPU, and disk for specified virtual machines
	 *
	 * @access       public
	 * @param        string
	 * @return       string
	 */
	public function getShareInfo($vmname)
	{
		// filters: memory[int,low,normal,high],cpu[int,low,normal,high],diskvalue[int,low,normal,high +diskname]
		$details = shell_exec("{$this->perl} {$this->sdk}vm/sharesmanager.pl --server {$this->host} --username {$this->username} --password '{$this->password}' --vmname {$vmname}");
		return $details;
	}

	/**
	 * Allows you to list, revert, go to, rename, or remove one or more snapshots
	 *
	 * @access       public
	 * @param        string
	 * @return       string
	 */
	public function getSnapshot($vmname = FALSE, $action = "list")
	{
		// filters: vmname,datacenter,pool,host,folder,ipaddress,powerstatus,guestos
		$valid = array("list", "revert", "goto", "rename", "remove", "removeall", "create");
		if(in_array($action, $valid))
		{
			$details = shell_exec("{$this->perl} {$this->sdk}vm/snapshotmanager.pl --server {$this->host} --username {$this->username} --password '{$this->password}' --operation {$action}".($vmname ? " --vmname {$vmname}" : ""));
			return $details;
		}
	}

	/**
	 * Creates snapshots of virtual machines
	 *
	 * @access       public
	 * @param        string
	 * @return       string
	 */
	public function vmSnapshot($snapshot_name)
	{
		// filters: vmname,powerstatus,ipaddress,guest_os -- host, datacenter, folder, pool
		$details = shell_exec("{$this->perl} {$this->sdk}vm/vmsnapshot.pl --server {$this->host} --username {$this->username} --password '{$this->password}' --sname {$snapshot_name} --vmname {$vmname}");
		return $details;
	}

	/**
	 * Create a new virtual disk on a virtual machine
	 *
	 * @access       public
	 * @param        string
	 * @return       string
	 */
	public function addVdisk($vmname, $filename)
	{
		// filters: disksize(mb),nopersis,independent,backingtype
		$details = shell_exec("{$this->perl} {$this->sdk}vm/vdiskcreate.pl --server {$this->host} --username {$this->username} --password '{$this->password}' --vmname {$vmname} --filename {$filename}");
		return $details;
	}

	/**
	 * Perform clone operation on virtual machine and customize operation on both virtual machine and the guest
	 *
	 * @access       public
	 * @param        string
	 * @return       string
	 */
	public function vmClone($vmhost, $vmname, $vmname_destination)
	{
		// filters: datastore
		$details = shell_exec("{$this->perl} {$this->sdk}vm/vmclone.pl --server {$this->host} --username {$this->username} --password '{$this->password}' --vmname {$vmname} --vmhost {$vmhost} --vmname_destination {$vmname_destination}");
		return $details;
	}

	/**
	 * Perform poweron, poweroff, suspend, reset, reboot, shutdown and standby operations on virtual machines
	 *
	 * @access       public
	 * @param        string
	 * @return       string
	 */
	public function vmControl($action)
	{
		// filters: vmname, ipaddress, guestos, datacenter, pool, folder, host
		$valid = array("poweron", "poweroff", "suspend", "reboot", "reset", "shutdown", "standby");
		if(in_array($action, $valid))
		{
			$details = shell_exec("{$this->perl} {$this->sdk}vm/vmcontrol.pl --server {$this->host} --username {$this->username} --password '{$this->password}' --operation {$action}");
			return $details;
		}
	}

	/**
	 * Create Virtual Machines as per the specifications
	 *
	 * @access       public
	 * @param        string
	 * @return       string
	 */
	public function vmCreate($filename = NULL, $schema = NULL)
	{
		// http://pubs.vmware.com/vsphere-51/topic/com.vmware.perlsdk.uaref.doc/vmcreate.html
		$details = shell_exec("{$this->perl} {$this->sdk}vm/vmcreate.pl --server {$this->host} --username {$this->username} --password '{$this->password}'");
		return $details;
	}

	/**
	 * Migrates one or more virtual machines within the current host or from the current host to another host
	 *
	 * @access       public
	 * @param        string
	 * @return       string
	 */
	public function vmMigrate($vmname, $sourcehost, $targethost, $targetdatastore, $targetpool)
	{
		// filters: priority[default,high,low], state[poweredOn,poweredOff,suspended]
		$details = shell_exec("{$this->perl} {$this->sdk}vm/vmmigrate.pl --server {$this->host} --username {$this->username} --password '{$this->password}' --vmname {$vmname} --sourcehost {$sourcehost} --targethost {$targethost} --targetdatastore {$targetdatastore} --targetpool {$targetpool}");
		return $details;
	}

	/**
	 * Reconfigure a virtual machine
	 *
	 * @access       public
	 * @param        string
	 * @return       string
	 */
	public function vmReconfig($filename = NULL, $schema = NULL)
	{
		$details = shell_exec("{$this->perl} {$this->sdk}vm/vmreconfig.pl --server {$this->host} --username {$this->username} --password '{$this->password}'");
		return $details;
	}

	/**
	 * Registers or unregisters a virtual machine
	 *
	 * @access       public
	 * @param        string
	 * @return       string
	 */
	public function vmRegister($action)
	{
		if($action == "register" || $action == "unregister")
		{
			//filters: vmname,pool,hostname,vmxpath,datacenter

		}
	}

	/**
	 * Converts a virtual machine to a template and template back to virtual machine
	 *
	 * @access       public
	 * @param        string, string
	 * @return       string
	 */
	public function vmTemplate($vmname, $action)
	{
		if($action=="T" || $action=="VM")
		{
			// filters: pool, host
			$details = shell_exec("{$this->perl} {$this->sdk}vm/vmtemplate.pl --server {$this->host} --username {$this->username} --password '{$this->password}' --operation {$action} --vmname {$vmname}");
			return $details;
		}
	}

	/**
	 * Display the Processor, Network and Memory attributes of the hosts
	 *
	 * @access       public
	 * @param
	 * @return       string
	 */
	public function getHostDetails($host = FALSE)
	{
		$details = shell_exec("{$this->perl} {$this->sdk}host/hostinfo.pl --server {$this->host} --username {$this->username} --password '{$this->password}'".($host ? " --hostname {$host}" : ""));
		return $details;
	}

	/**
	 * Performs specified operation specified host
	 *
	 * @access       public
	 * @param        string
	 * @return       string
	 */
	public function hostOps($host, $action)
	{
		// http://pubs.vmware.com/vsphere-51/topic/com.vmware.perlsdk.uaref.doc/hostops.html
		$valid = array("add_standalone", "disconnect", "enter_maintenance", "exit_maintenance", "reboot", "shutdown", "addhost", "reconnect", "removehost", "moveintofolder", "moveintocluster");
		if(in_array($action, $valid))
		{
			// filters: folder --cluster --target_host --target_username --target_password

		}
	}

	/**
	 * Browse datastores and list their attributes
	 *
	 * @access       public
	 * @param        string
	 * @return       string
	 */
	public function hostBrowseDS($datastore = FALSE)
	{
		// WARNING: takes a long time for larger datastores unless filters are applied!!
		// filters: attributes,capacity,filetype,freespace,name
		$details = shell_exec("{$this->perl} {$this->sdk}host/dsbrowse.pl --server {$this->host} --username {$this->username} --password '{$this->password}'".($datastore ? " --name {$datastore}" : ""));
		return $details;
	}

	/**
	 * Migrates all virtual machines from one host to another
	 *
	 * @access       public
	 * @param        string
	 * @return       string
	 */
	public function hostEvacuate($sourcehost, $targethost, $targetdatastore, $targetpool)
	{
		// filters: priority, state
		$valid_priority = array("default", "high", "low");
		$valid_state = array("poweredOn", "poweredOff", "suspended");

		$details = shell_exec("{$this->perl} {$this->sdk}host/hostevacuate.pl --server {$this->host} --username {$this->username} --password '{$this->password}' --sourcehost {$sourcehost} --targethost {$targethost} --targetdatastore {$targetdatastore} --targetpool {$targetpool}");
		return $details;
	}

	/**
	 * Displays system information like the name, type, and version
	 *
	 * @access       public
	 * @param        string
	 * @return       string
	 */
	public function viVersion($service)
	{
		$valid = array("all", "apiinfo", "product", "version", "ostype");
		if(in_array($service, $valid))
		{
			$details = shell_exec("{$this->perl} {$this->sdk}general/viversion.pl --server {$this->host} --username {$this->username} --password '{$this->password}' --aboutservice {$service}");
			return $details;
		}
	}

	/**
	 * Retrieves performance counters from a host
	 *
	 * @access       public
	 * @param        string
	 * @return       string
	 */
	public function viPerformance($host, $counter)
	{
		// interval, samples, instance, out
		$valid = array("cpu", "mem", "net", "disk", "sys");
		if(in_array($counter, $valid))
		{
			$details = shell_exec("{$this->perl} {$this->sdk}performance/viperformance.pl --server {$this->host} --username {$this->username} --password '{$this->password}' --host {$host} --countertype {$counter}");
			return $details;
		}
	}
}
