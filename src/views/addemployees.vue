<template>
  <div class="container">
    <h2>เพิ่มพนักงาน</h2>
    
    <form @submit.prevent="addEmployee">
      <div class="form-group">
        <input 
          v-model="employee.first_name" 
          type="text"
          placeholder="ชื่อ" 
          required 
        />
      </div>
      
      <div class="form-group">
        <input 
          v-model="employee.last_name" 
          type="text"
          placeholder="นามสกุล" 
          required 
        />
      </div>
      
      <div class="form-group">
        <textarea 
          v-model="employee.address" 
          placeholder="ที่อยู่" 
          rows="3"
          required 
        ></textarea>
      </div>
      
      <div class="form-group">
        <input 
          v-model="employee.phone" 
          type="text"
          placeholder="เบอร์โทรศัพท์" 
          maxlength="10"
          required 
        />
      </div>
      
      <div class="form-group">
        <input 
          type="file" 
          @change="onFileChange" 
          ref="fileInput"
          accept="image/*"
          required 
        />
      </div>

      <!-- แสดงตัวอย่างรูปภาพ -->
      <div v-if="imagePreview" class="image-preview">
        <img :src="imagePreview" alt="Preview" />
      </div>
      
      <div class="form-buttons">
        <button type="submit">บันทึก</button>
        <button type="button" @click="resetForm">ยกเลิก</button>
        <button type="button" @click="goBack">กลับ</button>
      </div>
    </form>

    <div v-if="message" class="message" :class="{ success: isSuccess, error: !isSuccess }">
      {{ message }}
    </div>
  </div>
</template>

<script>
import { useRouter } from 'vue-router';

export default {
  setup() {
    const router = useRouter();
    return { router };
  },
  data() {
    return {
      employee: {
        first_name: "",
        last_name: "",
        address: "",
        phone: "",
        image: null,
      },
      message: "",
      isSuccess: false,
      imagePreview: null,
    };
  },
  methods: {
    onFileChange(event) {
      const file = event.target.files[0];
      this.employee.image = file;
      
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          this.imagePreview = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    },
    
    resetForm() {
      this.employee = {
        first_name: "",
        last_name: "",
        address: "",
        phone: "",
        image: null,
      };
      this.message = "";
      this.isSuccess = false;
      this.imagePreview = null;
      this.$refs.fileInput.value = "";
    },

    goBack() {
      this.router.push('/employees');
    },

    async addEmployee() {
      try {
        const formData = new FormData();
        formData.append("first_name", this.employee.first_name);
        formData.append("last_name", this.employee.last_name);
        formData.append("address", this.employee.address);
        formData.append("phone", this.employee.phone);
        formData.append("image", this.employee.image);

        console.log("กำลังส่งข้อมูลไป API...");

        const response = await fetch("http://localhost:8081/final67709463/php/addemployees.php", {
          method: "POST",
          body: formData,
        });

        console.log("Response status:", response.status);

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log("Response data:", data);
        
        this.message = data.message;
        this.isSuccess = data.success;

        if (data.success) {
          setTimeout(() => {
            this.router.push('/employees');
          }, 1500);
        }

      } catch (error) {
        console.error('Detailed Error:', error);
        this.message = "เกิดข้อผิดพลาดในการเชื่อมต่อ: " + error.message;
        this.isSuccess = false;
      }
    },
  },
};
</script>

<style>
.container {
  max-width: 500px;
  margin: 50px auto;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 8px;
}

.form-group {
  margin-bottom: 15px;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-family: inherit;
}

.form-group textarea {
  resize: vertical;
}

.image-preview {
  text-align: center;
  margin-bottom: 15px;
}

.image-preview img {
  max-width: 200px;
  max-height: 200px;
  border-radius: 5px;
  border: 1px solid #ddd;
}

.form-buttons {
  text-align: center;
  margin-top: 20px;
}

.form-buttons button {
  margin: 0 5px;
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.form-buttons button[type="submit"] {
  background-color: #007bff;
  color: white;
}

.form-buttons button[type="button"] {
  background-color: #6c757d;
  color: white;
}

.message {
  margin-top: 15px;
  padding: 10px;
  border-radius: 4px;
  text-align: center;
}

.message.success {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.message.error {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}
</style>