<template>
  <div class="container-fluid mt-4">
    <div class="text-center mb-3">
      <h3 class="mt-2">Employee List </h3>
    </div>
    
    <div class="mb-3">
      <button class="btn btn-primary" @click="goToAddEmployee">Add+</button>
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
          <th>แก้ไข/ลบ</th>
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
          <td>
            <button class="btn btn-warning btn-sm me-2" style="background-color: #ffd700; border: none; color: #000;" @click="openEditModal(employee)">
              Edit
            </button>
            <button 
              class="btn btn-danger btn-sm" 
              style="background-color: #dc3545; border: none;"
              @click="deleteEmployee(employee.emp_id)"
            >
              Delete
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="loading" class="text-center">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <div class="modal fade" id="editModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">แก้ไขข้อมูลพนักงาน</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="updateEmployee">
              <div class="mb-3">
                <label class="form-label">ชื่อ</label>
                <input v-model="editForm.first_name" type="text" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">นามสกุล</label>
                <input v-model="editForm.last_name" type="text" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">ที่อยู่</label>
                <textarea v-model="editForm.address" class="form-control" rows="3" required></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">เบอร์โทรศัพท์</label>
                <input v-model="editForm.phone" type="text" class="form-control" required />
              </div>
              
              <div class="mb-3">
                <label class="form-label">รูปภาพ</label>
                <input type="file" @change="handleFileUpload" class="form-control" />
                <div v-if="editForm.image">
                  <p class="mt-2">รูปเดิม:</p>
                  <img :src="'http://localhost:8081/final67709463/php/uploads/' + editForm.image" width="60" height="80" style="object-fit: cover; border-radius: 5px;"/>
                </div>
              </div>
              <button type="submit" class="btn btn-success">บันทึกการแก้ไข</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from "vue";
import { Modal } from "bootstrap";
import { useRouter } from "vue-router"; 

export default {
  name: "EditEmployeeList",
  setup() {
    const router = useRouter(); 
    const employees = ref([]);
    const loading = ref(true);
    const error = ref(null);
    const API_URL = "http://localhost:8081/final67709463/php/employees.php";


    const editForm = ref({
      emp_id: null,
      first_name: "",
      last_name: "",
      address: "",
      phone: "",
      image: ""
    });
    const newImageFile = ref(null);
    let modalInstance = null;


    const formatEmpId = (id) => {
      return String(id).padStart(8, '0');
    };


    const goToAddEmployee = () => {
      router.push('/addemployees'); 
    };


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


    const openEditModal = (employee) => {
      editForm.value = { ...employee };
      newImageFile.value = null;
      const modalEl = document.getElementById("editModal");
      modalInstance = new Modal(modalEl);
      modalInstance.show();
    };

    const handleFileUpload = (event) => {
      newImageFile.value = event.target.files[0];
    };


    const updateEmployee = async () => {
      const formData = new FormData();
      formData.append("action", "update");
      formData.append("emp_id", editForm.value.emp_id);
      formData.append("first_name", editForm.value.first_name);
      formData.append("last_name", editForm.value.last_name);
      formData.append("address", editForm.value.address);
      formData.append("phone", editForm.value.phone);
      formData.append("old_image", editForm.value.image);
      
      if (newImageFile.value) {
        formData.append("image", newImageFile.value);
      }

      try {
        const res = await fetch(API_URL, {
          method: "POST",
          body: formData
        });
        const result = await res.json();
        if (result.message) {
          alert(result.message);
          fetchEmployees();
          modalInstance.hide();
        } else if (result.error) {
          alert(result.error);
        }
      } catch (err) {
        alert(err.message);
      }
    };


    const deleteEmployee = async (id) => {
      if (!confirm("คุณแน่ใจหรือไม่ที่จะลบพนักงานนี้?")) return;

      const formData = new FormData();
      formData.append("action", "delete");
      formData.append("emp_id", id);

      try {
        const res = await fetch(API_URL, {
          method: "POST",
          body: formData
        });
        const result = await res.json();
        if (result.message) {
          alert(result.message);

          fetchEmployees(); 
        } else if (result.error) {
          alert(result.error);
        }
      } catch (err) {
        alert(err.message);
      }
    };

    onMounted(fetchEmployees);

    return {
      employees,
      loading,
      error,
      editForm,
      openEditModal,
      handleFileUpload,
      updateEmployee,
      deleteEmployee,
      formatEmpId, 
      goToAddEmployee
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


.btn-warning {
  color: #000;
}
</style>