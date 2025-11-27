<template>
  <div class="container-fluid mt-4">
    <div class="text-center mb-3">
      <h3 class="mt-2">Show Employee List</h3>
    </div>
    
    <table class="table table-bordered">
      <thead style="background-color: #d4e6f7;">
        <tr>
          <th>รูปภาพ</th>
          <th>รหัส</th>
          <th>ชื่อ</th>
          <th>นามสกุล</th>
          <th>ที่อยู่</th>
          <th>เบอร์โทรศัพท์</th>
          </tr>
      </thead>
      <tbody>
        <tr v-for="employee in employees" :key="employee.emp_id">
          <td class="text-center">
            <img 
              :src="'http://localhost:8081/final67709463/php/uploads/' + employee.image" 
              width="60" 
              height="80"
              style="object-fit: cover; border-radius: 5px;"
              alt="Employee"
            />
          </td>
          <td>{{ formatEmpId(employee.emp_id) }}</td>
          <td>{{ employee.first_name }}</td>
          <td>{{ employee.last_name }}</td>
          <td>{{ employee.address }}</td>
          <td>{{ employee.phone }}</td>
          </tr>
      </tbody>
    </table>

    <div v-if="loading" class="text-center">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    </div>
</template>

<script>
import { ref, onMounted } from "vue";


export default {
  name: "ShowEmployeeList", 
  setup() {
    const employees = ref([]);
    const loading = ref(true);
    const error = ref(null);

    const API_URL = "http://localhost:8081/final67709463/php/showemployees.php"; 


    const formatEmpId = (id) => {
      return String(id).padStart(8, '0');
    };


    // ดึงข้อมูลพนักงาน (คงไว้)
    const fetchEmployees = async () => {
      try {
        const res = await fetch(API_URL);
        const data = await res.json();
        employees.value = data.success ? data.data : [];
      } catch (err) {
        error.value = err.message;
      } finally {
        loading.value = false;
      }
    };


    onMounted(fetchEmployees);

    return {
      employees,
      loading,
      error,
      formatEmpId,
    };
  }
};
</script>

<style scoped>
.table thead th {
  font-weight: 600;
  text-align: center;
  vertical-align: middle;
}

.table tbody td {
  vertical-align: middle;
  text-align: center;
}
</style>