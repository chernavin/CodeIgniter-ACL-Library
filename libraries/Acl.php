<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Acl Class
 *
 * Enables to control user access
 *
 * @version		1.0 beta
 * @author 		Aleksandr Chernavin <chernavin.a.a@mail.ru>
 * @link		https://github.com/chernavin/CodeIgniter-ACL-Library
 */
class Acl {

	// Rule types
	const ALLOW = TRUE;
	const DENY  = FALSE;
	
	// Resource list
	private $_resources = array();
	// Access control rules
	private $_rules = array(
		'access' => self::DENY,		
		'roles' => array(
		/*
			'role_1' => array(
				'access' => self::DENY,
				'resources' => array(
					'res_1' => array(
						'access' => self::DENY,
						'privileges' => array(
							'piv_1' => self::DENY,							
							...
						)
					),
					
					...
				)
			),
			
			...
		*/
		)
	);
	
	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	public function __construct()
	{
		log_message('debug', 'Acl Class Initialized');
	}
	
	/**
	 * Get private var
	 *
	 * @access	public
	 * @param	string
	 * @return	mixed
	 */
	public function __get($name) 
	{
		return isset($this->$name) ? $this->$name : NULL;
	}

	/**
	 * Add a role
	 *
	 * Roles should be added the first
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	public function add_role($role_id)
	{
		// Check role id
		if ( ! is_string($role_id))
		{
			show_error('Role id should be a string');
		}		
		
		// If a role is already exists
		if ($this->_role_exists($role_id))
		{
			show_error('The role "' . $role_id . '" is already exists');
		}
		else
		{
			// Add a role
			$this->_rules['roles'][$role_id] = array(
				'access' => self::DENY,
				'resources' => array()
			);
		}
	}
	
	/**
	 * Add a resource
	 *
	 * Resources should be added the second
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	public function add_resource($resource_id)
	{
		// Check resource id
		if ( ! is_string($resource_id))
		{
			show_error('Resource id should be a string');
		}
		
		// If a resource is already exists
		if ($this->_resource_exists($resource_id))
		{
			show_error('The resource "' . $resource_id . '" is already exists');
		}
		else
		{
			// Add to list
			$this->_resources[] = $resource_id;
			
			// Add to all roles
			foreach ($this->_rules['roles'] as &$_role)
			{
				$_role['resources'][$resource_id] = array(
					'access' => self::DENY,
					'privileges' => array()
				);
			}
				
			// Break the reference
			unset($_role);
		}
	}
	
	/**
	 * Add an "allow" rule
	 *
	 * Rules should be added the third
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	mixed
	 * @return	void
	 */
	public function allow($role_id = NULL, $resource_id = NULL, $privilege_id = NULL)
	{
		$this->_set_rule(self::ALLOW, $role_id, $resource_id, $privilege_id);
	}
	
	/**
	 * Add a "deny" rule
	 *
	 * Rules should be added the third
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	mixed
	 * @return	void
	 */
	public function deny($role_id = NULL, $resource_id = NULL, $privilege_id = NULL)
	{
		$this->_set_rule(self::DENY, $role_id, $resource_id, $privilege_id);
	}
	
	/**
	 * Return "true" if the role has access to the resource/privilege
	 *
	 * =============================================================
	 *  role   | resource | privilege | return
	 * =============================================================
	 *  null   | mixed    | mixed     | global access
	 * -------------------------------------------------------------
	 *  string | null     | mixed     | global role access
	 * -------------------------------------------------------------
	 *  string | string   | null      | global role/resource access
	 * -------------------------------------------------------------
	 *  string | string   | string    | appropriate access rule
	 * -------------------------------------------------------------
	 *
	 * If the role/resource/privilege is not defined it will be set as "null"
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	public function is_allowed($role_id = NULL, $resource_id = NULL, $privilege_id = NULL)
	{
		// If not defined then set "null"
		$role_id = $this->_role_exists($role_id) ? $role_id : NULL;
		$resource_id = $this->_resource_exists($resource_id) ? $resource_id : NULL;
		
		// Global access
		if ($role_id === NULL)
		{
			return $this->_rules['access'] === self::ALLOW;
		}
		else
		{
			// Global role access
			if ($resource_id === NULL)
			{
				return $this->_rules['roles'][$role_id]['access'] === self::ALLOW;
			}
			else
			{
				// If privilege exists
				if (isset($this->_rules['roles'][$role_id]['resources'][$resource_id]['privileges'][$privilege_id]))
				{
					return $this->_rules['roles'][$role_id]['resources'][$resource_id]['privileges'][$privilege_id] === self::ALLOW;
				}
				else
				{
					// Global role/resource access
					return $this->_rules['roles'][$role_id]['resources'][$resource_id]['access'] === self::ALLOW;
				}
			}			
		}
	}
	
	/**
	 * Set access rule
	 *
	 * =============================================================
	 *  role   | resource | privilege | action
	 * =============================================================
	 *  null   | null     | mixed     | global access,
	 *         |          |           | global role access,
	 *         |          |           | global role/resource access
	 * -------------------------------------------------------------
	 *  string | null     | null      | global role access,
	 *         |          |           | global role/resource access,
	 *         |          |           | role/all res./all priv.
	 * -------------------------------------------------------------
	 *  string | null     | string    | global role access,
	 *         |          |           | role/all res./priv.
	 * -------------------------------------------------------------
	 *  null   | string   | null      | global role/resource access,
	 *         |          |           | all roles/res./all priv.
	 * -------------------------------------------------------------
	 *  null   | string   | string    | all roles/res./priv.
	 * -------------------------------------------------------------
	 *  string | string   | null      | global role/resource access,
	 *         |          |           | role/res./all priv.
	 * -------------------------------------------------------------
	 *  string | string   | string    | role/res./priv.
	 * -------------------------------------------------------------
	 * 
	 * If the role/resource/privilege is "null" it will be mean "all"
	 *
	 * @access	private
	 * @param	mixed
	 * @param	string
	 * @param	string
	 * @param	mixed
	 * @return	void
	 */
	private function _set_rule($rule, $role_id = NULL, $resource_id = NULL, $privilege_id = NULL)
	{
		// For all rules
		if ($role_id === NULL && $resource_id === NULL)
		{
			// Global access
			$this->_rules['access'] = $rule;
			
			foreach ($this->_rules['roles'] as &$_role)
			{
				// Global role access
				$_role['access'] = $rule;
				
				// Global role/resource access
				foreach ($_role['resources'] as &$_resource)
				{
					$_resource['access'] = $rule;
				}
			}
			
			// Break the references
			unset($_role);
			unset($_resource);
		}
		else
		{
			$_roles = array();
			$_resources = array();			
			$_privileges = array();			
			
			// For all roles
			if ($role_id === NULL)
			{
				$_roles = array_keys($this->_rules['roles']);
			}
			else
			{
				// Check role id
				if ( ! is_string($role_id))
				{
					show_error('Role id should be a string');
				}
				
				// Current role if exists
				if ($this->_role_exists($role_id))
				{
					$_roles[] = $role_id;
					
					// Global role access
					if ($resource_id === NULL)
					{
						$this->_rules['roles'][$role_id]['access'] = $rule;
					}
				}
				else
				{
					log_message('error', 'The role "' . $role_id . '" is not defined');
				}
			}
			
			// For all resources
			if ($resource_id === NULL)
			{
				$_resources = $this->_resources;
			}
			else
			{
				// Check resource id
				if ( ! is_string($resource_id))
				{
					show_error('Resource id should be a string');
				}
		
				// Current resource if exists
				if ($this->_resource_exists($resource_id))
				{
					$_resources[] = $resource_id;
				}
				else
				{
					log_message('error', 'The resource "' . $resource_id . '" is not defined');
				}
			}
				
			// Normalize privileges
			if (is_array($privilege_id))
			{
				$_privileges = $privilege_id;
			}
			else
			{
				if (is_string($privilege_id))
				{
					$_privileges[] = $privilege_id;
				}
			}
			
			// Set access rule
			foreach ($_roles as $_role_id)
			{
				foreach ($_resources as $_resource_id)
				{
					// Current resource
					$_resource = &$this->_rules['roles'][$_role_id]['resources'][$_resource_id];
				
					// For all privileges
					if ($privilege_id === NULL)
					{
						// Global role/resource access
						$_resource['access'] = $rule;
						
						foreach ($_resource['privileges'] as &$_privilege)
						{
							$_privilege = $rule;
						}
						
						// Break the reference
						unset($_privilege);
					}
					else
					{
						foreach ($_privileges as $_privilege_id)
						{	
							if (is_string($_privilege_id))
							{
								$_resource['privileges'][$_privilege_id] = $rule;
							}
						}
					}
					
					// Break the reference
					unset($_resource);
				}
			}
		}
	}
	
	/**
	 * Verify existence of a role
	 *
	 * @access	private
	 * @param	string
	 * @return	bool
	 */
	private function _role_exists($role_id)
	{
		return isset($this->_rules['roles'][$role_id]);
	}
	
	/**
	 * Verify existence of a resource
	 *
	 * @access	private
	 * @param	string
	 * @return	bool
	 */
	private function _resource_exists($resource_id)
	{
		return in_array($resource_id, $this->_resources);
	}

}
// END Acl Class

/* End of file Acl.php */
/* Location: ./application/libraries/Acl.php */