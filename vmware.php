<?php

/**
 * PHP Wrapper of VmWare vSphere Perl SDK
 *
 * Wrapper class for easy interfacing VmWare vSphere data into your PHP application. 
 * Note, this requires the Vmware vSphere Perl SDK. Not all functionality provided 
 * by vSphere or the Perl SDK is implemented. 
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
	function __construct($host, $username, $password, $perl='/path/to/perl', $sdk='/path/to/vsphere/perl/sdk')
	{		
		$this->host = $host;
		$this->username = $username;
		$this->password = $password;
		$this->perl = $perl;
		$this->sdk = $sdk;
	}

	/**
	* Internal function to execute specified perl script with specified arguments and return the output
	*
	* @access 		public
	* @param 		string
	* @param 		array
	* @return 		string
	*/
	private function vQuery($script, Array $kvData=[]) 
	{
		$exec_str = "{$this->perl} {$this->sdk}{$script} --server {$this->host} --username '{$this->username}' --password '{$this->password}'";
		foreach($kvData as $key=>$val)
			$exec_str .= " --{$key} '{$val}'";
		
		return shell_exec($exec_str);
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
		$script = 'vm/vminfo.pl';
		$data['vmname'] = $vmname;
		return $this->vQuery($script, $data);
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
			$script = 'vm/guestinfo.pl';
			$data['vmname'] = $vmname;
			$data['operation'] = $action;
			return $this->vQuery($script, $data)
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
		$script = 'vm/sharesmanager.pl';
		$data = ['vmname'] = $vmname;
		return $this->vQuery($script, $data);
	}

	/**
	 * Allows you to list, revert, go to, rename, or remove one or more snapshots
	 *
	 * @access       public
	 * @param        string
	 * @return       string
	 */
	public function getSnapshot($vmname = NULL, $action = "list")
	{
		// filters: vmname,datacenter,pool,host,folder,ipaddress,powerstatus,guestos
		$valid = array("list", "revert", "goto", "rename", "remove", "removeall", "create");
		if(in_array($action, $valid))
		{
			$script = 'vm/snapshotmanager.pl';
			$data['operation'] = $action;
			if($vmname)
				$data['vmname'] = $vmname;
			return $this->vQuery($script, $data);
		}
	}

	/**
	 * Creates snapshots of virtual machines
	 *
	 * @access       public
	 * @param        string
	 * @return       string
	 */
	public function vmSnapshot($vmname, $snapshot_name)
	{
		// filters: vmname,powerstatus,ipaddress,guest_os -- host, datacenter, folder, pool
		$script = 'vm/vmsnapshot.pl';
		$data['vmname'] = $vmname;
		$data['sname'] = $snapshot_name;
		return $this->vQuery($script, $data);
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
		$script = 'vm/vdiskcreate.pl';
		$data['vmname'] = $vmname;
		$data['filename'] = $filename;
		return $this->vQuery($script, $data);
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
		$script = 'vm/vmclone.pl';
		$data['vmname'] = $vmname;
		$data['vmhost'] = $vmhost;
		$data['vmname_destination']	= $vmname_destination;	
		return $this->vQuery($script, $data);
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
			$script = 'vm/vmcontrol.pl';
			$data['operation'] = $action;
			return $this->vQuery($script, $data);
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
		$script = 'vm/vmcreate.pl';
		$data = array();
		if($filename)
			$data['filename'] = $filename;
		if($schema)
			$data['schema'] = $schema;
		return $this->vQuery($script, $data);
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
		$script = 'vm/vmmigrate.pl';
		$data['vmname'] = $vmname;
		$data['sourcehost'] = $sourcehost;
		$data['targethost'] = $targethost;
		$data['targetdatastore'] = $targetdatastore;
		$data['targetpool'] = $targetpool;
		return $this->vQuery($script, $data);
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
		$script = 'vm/vmreconfig.pl';
		$data = array();
		if($filename)
			$data['filename'] = $filename;
		if($schema)
			$data['schema'] = $schema;
		return $this->vQuery($script, $data);			
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
			$script = 'vm/vmtemplate.pl';
			$data['vmname'] = $vmname;
			$data['operation'] = $action;
			return $this->vQuery($script, $data);
		}
	}

	/**
	 * Display the Processor, Network and Memory attributes of the hosts
	 *
	 * @access       public
	 * @param
	 * @return       string
	 */
	public function getHostDetails($host = NULL)
	{
		$script = 'host/hostinfo.pl';
		$data = array();
		if($host)
			$data['host'] = $host;
		return $this->vQuery($script, $data);
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
	public function hostBrowseDS($datastore = NULL)
	{
		// WARNING: takes a long time for larger datastores unless filters are applied!!
		// filters: attributes,capacity,filetype,freespace,name
		$script = 'host/dsbrowse.pl';
		$data = array();
		if($datastore)
			$data['datastore'] = $datastore;
		return $this->vQuery($script, $data);
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

		$script = 'host/hostevacuate.pl';
		$data['sourcehost'] = $sourcehost;
		$data['targethost'] = $targethost;
		$data['targetdatastore'] = $targetdatastore;
		$data['targetpool'] = $targetpool;
		return $this->vQuery($script, $data);
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
			$script = 'general/viversion.pl';
			$data['aboutservice'] = $service;
			return $this->vQuery($script, $data);
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
			$script = 'performance/viperformance.pl';
			$data['host'] = $host;
			$data['countertype'] = $counter;
			return $this->vQuery($script, $data);
		}
	}
}
