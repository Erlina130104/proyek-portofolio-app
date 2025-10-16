// services/employeeService.js
// Dummy service for localStorage-based employee management
// Bisa diganti dengan axios API calls ke backend nanti

const employeeService = {
  /**
   * Get all employees with optional filters
   */
  getAll: async (params = {}) => {
    try {
      // Simulate API delay
      await new Promise(resolve => setTimeout(resolve, 300));
      
      const stored = localStorage.getItem('employeesData');
      const employees = stored ? JSON.parse(stored) : [];
      
      return {
        data: {
          success: true,
          data: {
            data: employees,
            pagination: {
              total: employees.length,
              per_page: 10,
              current_page: 1
            }
          }
        }
      };
    } catch (error) {
      console.error('Error fetching employees:', error);
      throw error;
    }
  },

  /**
   * Get employee by ID
   */
  getById: async (id) => {
    try {
      const stored = localStorage.getItem('employeesData');
      const employees = stored ? JSON.parse(stored) : [];
      const employee = employees.find(e => e.id === id);
      
      return {
        data: {
          success: !!employee,
          data: employee || null
        }
      };
    } catch (error) {
      console.error('Error fetching employee:', error);
      throw error;
    }
  },

  /**
   * Create new employee
   */
  create: async (data) => {
    try {
      const stored = localStorage.getItem('employeesData');
      const employees = stored ? JSON.parse(stored) : [];
      
      const newEmployee = {
        id: Date.now(),
        ...data,
        created_at: new Date().toISOString()
      };
      
      employees.unshift(newEmployee);
      localStorage.setItem('employeesData', JSON.stringify(employees));
      
      return {
        data: {
          success: true,
          message: 'Employee created successfully',
          data: newEmployee
        }
      };
    } catch (error) {
      console.error('Error creating employee:', error);
      throw error;
    }
  },

  /**
   * Update employee
   */
  update: async (id, data) => {
    try {
      const stored = localStorage.getItem('employeesData');
      const employees = stored ? JSON.parse(stored) : [];
      
      const index = employees.findIndex(e => e.id === id);
      if (index === -1) {
        throw new Error('Employee not found');
      }
      
      employees[index] = {
        ...employees[index],
        ...data,
        updated_at: new Date().toISOString()
      };
      
      localStorage.setItem('employeesData', JSON.stringify(employees));
      
      return {
        data: {
          success: true,
          message: 'Employee updated successfully',
          data: employees[index]
        }
      };
    } catch (error) {
      console.error('Error updating employee:', error);
      throw error;
    }
  },

  /**
   * Delete employee
   */
  delete: async (id) => {
    try {
      const stored = localStorage.getItem('employeesData');
      const employees = stored ? JSON.parse(stored) : [];
      
      const filtered = employees.filter(e => e.id !== id);
      localStorage.setItem('employeesData', JSON.stringify(filtered));
      
      return {
        data: {
          success: true,
          message: 'Employee deleted successfully'
        }
      };
    } catch (error) {
      console.error('Error deleting employee:', error);
      throw error;
    }
  },

  /**
   * Search employees
   */
  search: async (query) => {
    try {
      const stored = localStorage.getItem('employeesData');
      const employees = stored ? JSON.parse(stored) : [];
      
      const searchLower = query.toLowerCase();
      const filtered = employees.filter(e =>
        e.name.toLowerCase().includes(searchLower) ||
        e.email.toLowerCase().includes(searchLower) ||
        e.position.toLowerCase().includes(searchLower) ||
        e.department.toLowerCase().includes(searchLower)
      );
      
      return {
        data: {
          success: true,
          data: filtered
        }
      };
    } catch (error) {
      console.error('Error searching employees:', error);
      throw error;
    }
  },

  /**
   * Get employees by department
   */
  getByDepartment: async (department) => {
    try {
      const stored = localStorage.getItem('employeesData');
      const employees = stored ? JSON.parse(stored) : [];
      
      const filtered = employees.filter(e => 
        e.department.toLowerCase() === department.toLowerCase()
      );
      
      return {
        data: {
          success: true,
          data: filtered
        }
      };
    } catch (error) {
      console.error('Error fetching employees by department:', error);
      throw error;
    }
  },

  /**
   * Get employee statistics
   */
  getStats: async () => {
    try {
      const stored = localStorage.getItem('employeesData');
      const employees = stored ? JSON.parse(stored) : [];
      
      const stats = {
        total: employees.length,
        active: employees.filter(e => e.status === 'active').length,
        inactive: employees.filter(e => e.status === 'inactive').length,
        departments: [...new Set(employees.map(e => e.department))].length
      };
      
      return {
        data: {
          success: true,
          data: stats
        }
      };
    } catch (error) {
      console.error('Error fetching employee stats:', error);
      throw error;
    }
  },

  /**
   * Update employee status
   */
  updateStatus: async (id, status) => {
    try {
      const stored = localStorage.getItem('employeesData');
      const employees = stored ? JSON.parse(stored) : [];
      
      const index = employees.findIndex(e => e.id === id);
      if (index === -1) {
        throw new Error('Employee not found');
      }
      
      employees[index].status = status;
      employees[index].updated_at = new Date().toISOString();
      
      localStorage.setItem('employeesData', JSON.stringify(employees));
      
      return {
        data: {
          success: true,
          message: 'Employee status updated successfully',
          data: employees[index]
        }
      };
    } catch (error) {
      console.error('Error updating employee status:', error);
      throw error;
    }
  }
};

export default employeeService;