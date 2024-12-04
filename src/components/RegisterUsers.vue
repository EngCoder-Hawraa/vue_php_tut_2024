<template>
  <div>
    <h1 className="text-center">
      CRUD PHP, VUE.JS 3 & MYSQL
      <span className="smaller"> By Hawraa Arkan</span>
    </h1>
  </div>
  <div class="container" v-if="!isDBConnected">
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ isDBConnectedMsg }}
      <button
        type="button"
        class="btn-close"
        data-bs-dismiss="alert"
        aria-label="Close"
      ></button>
    </div>
  </div>
  <div class="container" v-else>
    <!-- Show Add student button -->
    <div class="col-lg-12">
      <button class="float-end btn btn-info" @click="addModalDialog(true)">
        <FIcons :icon="['fas', 'user']"></FIcons> Add New Student
      </button>
      <button
        class="float-start btn btn-danger"
        @click="deleteAllModalDialog(true)"
      >
        <FIcons :icon="['fas', 'user-times']" /> Delete All Student
      </button>
      <div class="clearfix"></div>
    </div>
    <hr class="bg-info" />
    <!-- Show Error Message -->
    <div class="container mt-3">
      <div
        class="alert alert-danger alert-dismissible fade show"
        role="alert"
        v-if="errMsg"
      >
        {{ errMsg }}
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="alert"
          aria-label="Close"
        ></button>
      </div>
      <div
        class="alert alert-success alert-dismissible fade show"
        role="alert"
        v-if="successMsg"
      >
        {{ successMsg }}
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="alert"
          aria-label="Close"
        ></button>
      </div>
    </div>
    <!-- Show Students Details : Tables -->
    <table class="table table-bordered table-striped caption-top">
      <caption>
        List of Registered Students ({{
          studentsData.length
        }})
      </caption>
      <thead>
        <tr>
          <th class="bg-success text-light">
            <FIcons class="no-bg" :icon="['fas', 'id-badge']"></FIcons>
            ID
          </th>
          <th class="bg-success text-light">
            <FIcons :icon="['fas', 'user']"></FIcons>
            Name
          </th>
          <th class="bg-success text-light">
            <FIcons :icon="['fas', 'envelope']"></FIcons>
            Email
          </th>
          <th class="bg-success text-light">
            <FIcons :icon="['fas', 'phone']"></FIcons>
            Phone
          </th>
          <th class="bg-success text-light">
            <FIcons :icon="['fas', 'cog']"></FIcons>
            Actions
          </th>
        </tr>
      </thead>
      <tbody>
        <tr class="text-center" v-for="(student, i) in studentsData" :key="i">
          <td>{{ student.id }}</td>
          <td>{{ student.name }}</td>
          <td>{{ student.email }}</td>
          <td>{{ formatPhoneNumber(student.phone) }}</td>
          <td>
            <button
              class="btn btn-danger"
              @click="
                deleteModalDialog(true);
                selectStudent(student);
              "
            >
              <FIcons :icon="['fas', 'user-times']"></FIcons>
              Delete
            </button>
            &nbsp;
            <button
              class="btn btn-warning"
              @click="
                updateModalDialog(true);
                selectStudent(student);
              "
            >
              <FIcons :icon="['fas', 'user-edit']"></FIcons>
              Update
            </button>
          </td>
        </tr>
      </tbody>
    </table>
    <!-- Show Add New Student Modal -->
    <div id="overlay" v-if="showAddModal">
      <div class="modal fade show d-block" tabindex="-1" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title text-info">Add New User</h5>
              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
                @click="addModalDialog(false)"
              >
                <FIcons :icon="['fas', 'times']"></FIcons>
              </button>
            </div>
            <div class="modal-body p-4">
              <form action="#" method="post" @click.prevent>
                <div
                  class="form-floating mb-3"
                  :class="{ 'form-group-error': v$.newStudent.name.$error }"
                >
                  <input
                    type="text"
                    class="form-control"
                    id="studentNameFloat"
                    placeholder="John Doe"
                    v-model="newStudent.name"
                  />
                  <label for="studentNameFloat">
                    <FIcons :icon="['far', 'user']"></FIcons>
                    Student Name
                  </label>
                  <span class="error-feedback" v-if="v$.newStudent.name.$error">
                    {{ v$.newStudent.name.$errors[0].$message }}
                  </span>
                </div>
                <div
                  class="form-floating mb-3"
                  :class="{ 'form-group-error': v$.newStudent.email.$error }"
                >
                  <input
                    type="email"
                    class="form-control"
                    id="studentEmail"
                    placeholder="john.doe@example.com"
                    v-model="newStudent.email"
                  />
                  <label for="studentEmail">
                    <FIcons :icon="['far', 'envelope']"></FIcons>
                    Student Email
                  </label>
                  <span
                    class="error-feedback"
                    v-if="v$.newStudent.email.$error"
                  >
                    {{ v$.newStudent.email.$errors[0].$message }}
                  </span>
                </div>
                <div
                  class="form-floating mb-3"
                  :class="{ 'form-group-error': v$.newStudent.phone.$error }"
                >
                  <input
                    type="tel"
                    class="form-control"
                    id="studentPhoneFloat"
                    placeholder="(123) 456-7890"
                    v-model="newStudent.phone"
                  />
                  <label for="studentPhoneFloat">
                    <FIcons :icon="['fas', 'phone']"></FIcons>
                    Student Phone
                  </label>
                  <span
                    class="error-feedback"
                    v-if="v$.newStudent.phone.$error"
                  >
                    {{ v$.newStudent.phone.$errors[0].$message }}
                  </span>
                </div>
                <hr class="bg-info" />
                <div class="d-grid gap-2">
                  <button
                    type="submit"
                    class="btn btn-info"
                    @click="addNewUser()"
                  >
                    Add New User
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Show Edit Student Modal -->
    <div id="overlay" v-if="showUpdateModal">
      <div class="modal fade show d-block" tabindex="-1" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title text-warning">Update Current Student</h5>
              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
                @click="updateModalDialog(false)"
              >
                <FIcons :icon="['fas', 'times']"></FIcons>
              </button>
            </div>
            <div class="modal-body p-4">
              <form action="#" method="post" @submit.prevent>
                <div
                  class="form-floating mb-3"
                  :class="{ 'form-group-error': v$.currentStudent.name.$error }"
                >
                  <input
                    type="text"
                    class="form-control"
                    id="studentNameFloat"
                    placeholder="John Doe"
                    v-model="currentStudent.name"
                  />
                  <label for="studentNameFloat">
                    <FIcons :icon="['far', 'user']"></FIcons>
                    Student Name
                  </label>
                  <span
                    class="error-feedback"
                    v-if="v$.currentStudent.name.$error"
                  >
                    {{ v$.currentStudent.name.$errors[0].$message }}
                  </span>
                </div>
                <div
                  class="form-floating mb-3"
                  :class="{
                    'form-group-error': v$.currentStudent.email.$error,
                  }"
                >
                  <input
                    type="email"
                    class="form-control"
                    id="studentEmail"
                    placeholder="john.doe@example.com"
                    v-model="currentStudent.email"
                  />
                  <label for="studentEmail">
                    <FIcons :icon="['far', 'envelope']"></FIcons>
                    Student Email
                  </label>
                  <span
                    class="error-feedback"
                    v-if="v$.currentStudent.email.$error"
                  >
                    {{ v$.currentStudent.email.$errors[0].$message }}
                  </span>
                </div>
                <div
                  class="form-floating mb-3"
                  :class="{
                    'form-group-error': v$.currentStudent.phone.$error,
                  }"
                >
                  <input
                    type="tel"
                    class="form-control"
                    id="studentPhoneFloat"
                    placeholder="(123) 456-7890"
                    v-model="currentStudent.phone"
                  />
                  <label for="studentPhoneFloat">
                    <FIcons :icon="['fas', 'phone']"></FIcons>
                    Student Phone
                  </label>
                  <span
                    class="error-feedback"
                    v-if="v$.currentStudent.phone.$error"
                  >
                    {{ v$.currentStudent.phone.$errors[0].$message }}
                  </span>
                </div>
                <hr class="bg-info" />
                <div class="d-grid gap-2">
                  <button
                    type="submit"
                    class="btn btn-warning"
                    @click="updateUser()"
                  >
                    Update Current Student
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Show Delete Student Modal -->
    <div id="overlay" v-if="showDeleteModal">
      <div class="modal fade show d-block" tabindex="-1" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title text-danger">Delete Current Student</h5>
              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
                @click="deleteModalDialog(false)"
              >
                <FIcons :icon="['fas', 'times']"></FIcons>
              </button>
            </div>
            <div class="modal-body p-4">
              <form action="#" method="post" @click.prevent>
                <h6 class="text-danger">
                  Are you sure you want to delete this student?
                </h6>
                <p>
                  <span
                    ><FIcons :icon="['fas', 'user']"></FIcons>
                    {{ currentStudent.name }}</span
                  ><br />
                  <span
                    ><FIcons :icon="['fas', 'envelope']"></FIcons>
                    {{ currentStudent.email }}</span
                  ><br />
                  <span
                    ><FIcons :icon="['fas', 'phone']"></FIcons>
                    {{ formatPhoneNumber(currentStudent.phone) }}</span
                  >
                </p>
                <hr class="bg-info" />
                <div class="d-grid gap-2">
                  <button class="btn btn-danger" @click="deleteUser()">
                    Delete Current User
                  </button>
                  <button
                    class="btn btn-success"
                    @click="deleteModalDialog(false)"
                  >
                    No, Don't Delete
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Show Delete All Students Modal -->
    <div id="overlay" v-if="showDeleteAllModal">
      <div class="modal fade show d-block" tabindex="-1" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title text-danger">Delete All Students</h5>
              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
                @click="deleteAllModalDialog(false)"
              >
                <FIcons :icon="['fas', 'times']"></FIcons>
              </button>
            </div>
            <div class="modal-body p-4">
              <form action="#" method="post" @click.prevent>
                <h6 class="text-danger">
                  Are you sure you want to delete all students?
                </h6>
                <p>Deleting all students, will remove them from your system</p>
                <hr class="bg-info" />
                <div class="d-grid gap-2">
                  <button class="btn btn-danger" @click="deleteAllUsers()">
                    Delete All Students
                  </button>
                  <button
                    class="btn btn-success"
                    @click="deleteAllModalDialog(false)"
                  >
                    No, Don't Delete
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import useVuelidate from "@vuelidate/core";
import { email, required, minLength, maxLength } from "@vuelidate/validators";

export default {
  name: "RegisterUsers",
  data() {
    return {
      v$: useVuelidate(),
      errMsg: "",
      successMsg: "",
      isDBConnected: false,
      isDBConnectedMsg: "",
      studentsData: [],
      showAddModal: false,
      showUpdateModal: false,
      showDeleteModal: false,
      showDeleteAllModal: false,
      newStudent: {
        name: "",
        email: "",
        phone: "",
      },
      currentStudent: {},
    };
  },
  validations() {
    return {
      newStudent: {
        name: { required, minLength: minLength(5) },
        email: { required, email },
        phone: { required, minLength: minLength(10), maxLength: maxLength(10) },
      },
      currentStudent: {
        name: { required, minLength: minLength(5) },
        email: { required, email },
        phone: { required, minLength: minLength(10), maxLength: maxLength(10) },
      },
    };
  },
  mounted() {
    this.dbConnection();
    this.getStudentsData();
  },
  methods: {
    addModalDialog(val) {
      this.showAddModal = val;
    },
    updateModalDialog(val) {
      this.showUpdateModal = val;
    },
    deleteModalDialog(val) {
      this.showDeleteModal = val;
    },
    deleteAllModalDialog(val) {
      this.showDeleteAllModal = val;
    },
    formatPhoneNumber(num) {
      var cleaned = ("" + num).replace(/\D/g, "");
      var match = cleaned.match(/^(\d{3})(\d{3})(\d{4})$/);
      if (match) {
        return "(" + match[1] + ") " + match[2] + "-" + match[3];
      }
      return null;
    },
    async getStudentsData() {
      let res = await axios.get(
        `http://localhost/vue_php_tut_2024/src/api/students.php?action=read`
      );
      const restData = res.data;
      if (res.status == 200) {
        if (restData.error) {
          // if error
          this.errMsg = restData.message;
        } else {
          // if everything is ok
          this.studentsData = restData.students;
        }
      }
    },
    async deleteAllUsers() {
      this.deleteAllModalDialog(false);
      this.clearOldMsgs();
      let res = await axios.get(
        `http://localhost/vue_php_tut_2024/src/api/students.php?action=deleteAll`
      );
      const restData = res.data;
      if (res.status == 200) {
        if (restData.error) {
          // if error
          this.errMsg = restData.message;
        } else {
          // if everything is ok
          this.successMsg = restData.message;
          this.studentsData = "";
        }
      }
    },
    async deleteUser() {
      this.deleteModalDialog(false);
      this.clearOldMsgs();
      let formData = this.toFormData(this.currentStudent);
      let res = await axios.post(
        `http://localhost/vue_php_tut_2024/src/api/students.php?action=delete`,
        formData
      );
      const restData = res.data;
      if (res.status == 200) {
        if (restData.error) {
          // if error
          this.errMsg = restData.message;
        } else {
          // if everything is ok
          let newStudentsData = this.studentsData.filter(
            (ele) => ele.id != this.currentStudent.id
          );
          console.table(newStudentsData);
          this.successMsg = restData.message;
          this.currentStudent = {};
          this.studentsData = newStudentsData;
          // this.getStudentsData();
        }
      }
    },
    clearOldMsgs() {
      this.errMsg = "";
      this.successMsg = "";
    },
    async dbConnection() {
      try {
        let res = await axios.get(
          `http://localhost/vue_php_tut_2024/src/api/students.php`
        );
        const restData = res.data;

        console.log("Received data from API:", restData);

        // Set connection status and message
        this.isDBConnected = restData.is_db_connected;
        this.isDBConnectedMsg = restData.connection_msg;
      } catch (error) {
        // Set message if there’s a network or server error
        this.isDBConnected = false;
        this.isDBConnectedMsg =
          "Failed to connect to the server. Check your network or server configuration.";
        // console.error("Connection error:", error);
      }
    },
    toFormData(obj) {
      var fd = new FormData();
      for (var i in obj) {
        console.log(i + " => " + obj[i]);
        fd.append(i, obj[i]);
      }
      return fd;
    },
    async addNewUser() {
      this.clearOldMsgs();
      this.v$.newStudent.$validate(); // Ensure all fields are marked as touched
      if (!this.v$.newStudent.$error) {
        this.addModalDialog(false);
        console.log("Add New User: Validated Successfully");
        // Further logic to handle adding a new user goes here
        let formData = this.toFormData(this.newStudent);
        let res = await axios.post(
          `http://localhost/vue_php_tut_2024/src/api/students.php?action=create`,
          formData
        );
        const restData = res.data;
        if (res.status == 200) {
          if (restData.error) {
            // if error
            this.errMsg = restData.message;
          } else {
            // if everything is ok
            this.newStudent = { name: "", email: "", phone: "" };
            this.successMsg = restData.message;
            this.getStudentsData();
            this.v$.newStudent.$reset();
          }
          console.log("Update User: Validated Successfully");
          // Further logic to handle updating a user goes here
        } else {
          console.log("Update User: Not Validated");
        }
      } else {
        console.log("Add New User: Not Validated");
      }
    },
    async updateUser() {
      this.v$.currentStudent.$validate(); // Ensure all fields are marked as touched
      if (!this.v$.currentStudent.$error) {
        this.updateModalDialog(false);
        this.clearOldMsgs();
        let formData = this.toFormData(this.currentStudent);
        let res = await axios.post(
          `http://localhost/vue_php_tut_2024/src/api/students.php?action=update`,
          formData
        );
        const restData = res.data;
        if (res.status == 200) {
          if (restData.error) {
            // if error
            this.errMsg = restData.message;
          } else {
            // if everything is ok
            this.currentStudent = {};
            this.successMsg = restData.message;
            this.getStudentsData();
            this.v$.currentStudent.$reset();
          }
          console.log("Update User: Validated Successfully");
          // Further logic to handle updating a user goes here
        } else {
          console.log("Update User: Not Validated");
        }
      }
    },
    selectStudent(std) {
      this.currentStudent = std;
    },
  },
};
</script>

<style scoped>
h1 {
  background: cadetblue;
  color: #fff;
  padding: 8px;
  margin-top: 0;
  margin-bottom: 50px;
}
.smaller {
  font-size: 0.85rem;
}
#overlay {
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgb(0 0 0 / 47%);
}
.form-group-error {
  color: red;
}

.form-group-error input {
  border-color: red;
}
</style>
