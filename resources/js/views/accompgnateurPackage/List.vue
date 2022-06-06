<template>
  <div class="app-container">
    <div class="filter-container">
      <el-select
        v-model="query.user_id"
        :placeholder="$t('الحريف')"
        clearable
        style="width: 90px"
        class="filter-item"
        @change="handleFilter"
      >
        <el-option
          v-for="user in listuser"
          :key="'i'+user.id"
          :label="user.name | uppercaseFirst"
          :value="user.id"
        />
      </el-select>
      <el-select
        v-model="query.package_id"
        :placeholder="$t('الرحلة')"
        clearable
        style="width: 90px"
        class="filter-item"
        @change="handleFilter"
      >
        <el-option
          v-for="vol in listpackage"
          :key="'r'+vol.id"
          :label="vol.labelle | uppercaseFirst"
          :value="vol.id"
        />
      </el-select>
      <el-button
        v-waves
        class="filter-item"
        type="primary"
        icon="el-icon-search"
        @click="handleFilter"
      />
      <el-button
        class="filter-item"
        style="margin-left: 10px"
        type="primary"
        icon="el-icon-plus"
        @click="handleCreate"
      />
      <el-button
        v-waves
        :loading="downloading"
        class="filter-item"
        type="primary"
        icon="el-icon-download"
        @click="handleDownload"
      />
    </div>

    <el-table
      v-loading="loading"
      :data="list"
      border
      fit
      highlight-current-row
      style="width: 100%"
    >
      <el-table-column align="center" label="ID" width="80">
        <template slot-scope="scope">
          <span>{{ scope.row.index }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('اللقب & الاسم')" width="'20%'" align="center">
        <template slot-scope="{ row }">
          <span class="link-type" @click="handleUpdate(row)">{{
            row.user
          }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('رحلة ')" width="'20%'" align="center">
        <template slot-scope="scope">
          <span class="link-type" @click="handleUpdate(row)">{{
            scope.row.package
          }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('الحالة')" width="110px" align="center">
        <template slot-scope="scope">
          <span v-show="scope.row.etat == '0'"> مرفوض </span>
          <span v-show="scope.row.etat == '1'"> غير مؤكد </span>
          <span v-show="scope.row.etat == '2'"> مؤكد </span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="أجراءات" width="350">
        <template slot-scope="scope">
          <el-button
            v-show="scope.row.etat == '1'"
            class="filter-item"
            type="primary"
            size="mini"
            icon="el-icon-close"
            @click="invalider(scope.row)"
          />
          <el-button
            v-show="scope.row.etat == '1'"
            class="filter-item"
            type="primary"
            size="mini"
            icon="el-icon-success"
            @click="valider(scope.row)"
          />
          <el-button
            type="primary"
            size="small"
            icon="el-icon-edit"
            @click="handleUpdate(scope.row)"
          />
          <el-button
            type="danger"
            size="small"
            icon="el-icon-delete"
            @click="handleDelete(scope.row.id, scope.row.name)"
          />
        </template>
      </el-table-column>
    </el-table>

    <pagination
      v-show="total > 0"
      :total="total"
      :page.sync="query.page"
      :limit.sync="query.limit"
      @pagination="getList"
    />

    <el-dialog :title="'انشاء وصل جديد'" :visible.sync="dialogFormVisible">
      <div v-loading="userCreating" class="form-container">
        <el-form
          ref="userForm"
          :rules="rules"
          :model="newUser"
          label-position="top"
          label-width="150px"
          style="max-width: 1000px"
        >
          <el-row>
            <el-col :span="11">
              <el-form-item label="اختر حساب الحريف ">
                <el-select
                  v-model="temp.user_id"
                  placeholder="اختر حساب الحريف"
                >
                  <el-option
                    v-for="user in listuser"
                    :key="'F'+user.id"
                    :label="user.name"
                    :value="user.id"
                  />
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="2"> - </el-col>
            <el-col :span="11">
              <el-form-item label="اختر رحلة">
                <el-select v-model="temp.package_id" placeholder="اختر رحلة">
                  <el-option
                    v-for="vol in listpackage"
                    :key="'P'+vol.id"
                    :label="vol.labelle | uppercaseFirst"
                    :value="vol.id"
                  />
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="dialogStatus === 'create' ? createUser() : updateData()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import Resource from '@/api/resource';
import waves from '@/directive/waves'; // Waves directive
import permission from '@/directive/permission'; // Permission directive
import axios from 'axios';
import { ConfirmerAccomp, RefuserAccomp } from '@/api/accompagnateur.js';

const accompanateurPackageResource = new Resource('accompgnateursPackages');

export default {
  name: 'AccompgnagteurPackageList',
  components: { Pagination },
  directives: { waves, permission },
  data() {
    return {
      list: null,
      listuser: {
        id: '',
        name: '',
      },
      listpackage: {
        id: '',
        labelle: '',
      },
      total: 0,
      loading: true,
      downloading: false,
      userCreating: false,
      query: {
        page: 1,
        limit: 15,
        user_id: '',
        package_id: '',
      },
      newUser: {},
      temp: {
        id: undefined,
        user_id: '',
        package_id: '',
        etat: '1',
        role: 1,
        user: '',
        package: '',
      },
      dialogFormVisible: false,
      dialogPermissionVisible: false,
      dialogPermissionLoading: false,
      currentUserId: 0,
      currentUser: {
        name: '',
        permissions: [],
        rolePermissions: [],
      },
      rules: {},
      permissionProps: {
        children: 'children',
        label: 'name',
        disabled: 'disabled',
      },
      permissions: [],
      menuPermissions: [],
      otherPermissions: [],
    };
  },
  computed: {},
  created() {
    this.resetNewUser();
    this.getList();
    this.getListUser();
    this.getListPackages();
  },
  methods: {
    async getListUser() {
      const { data } = await axios.get('http://localhost:8000/api/accompgnateurList');
      this.listuser = data.data;
      console.log(this.listuser);
    },
    async getListPackages() {
      const { data } = await axios.get(
        'http://localhost:8000/api/PackagesList'
      );
      this.listpackage = data.data;
      console.log(this.listpackage);
    },
    async getList() {
      const { limit, page } = this.query;
      this.loading = true;
      const { data, meta } = await accompanateurPackageResource.list(this.query);
      this.list = data;
      this.list.forEach((element, index) => {
        element['index'] = (page - 1) * limit + index + 1;
      });
      this.total = meta.total;
      this.loading = false;
    },
    handleFilter() {
      this.query.page = 1;
      this.getList();
    },
    handleUpdate(row) {
      this.temp = Object.assign({}, row); // copy obj
      this.dialogStatus = 'update';
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['userForm'].clearValidate();
      });
    },
    handleCreate() {
      this.resetNewUser();
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['userForm'].clearValidate();
      });
    },
    handleDelete(id, name) {
      this.$confirm('هل تريد حذف هذا الوصل', 'تحذير', {
        confirmButtonText: 'نعم',
        cancelButtonText: 'لا',
        type: 'warning',
      })
        .then(() => {
          accompanateurPackageResource
            .destroy(id)
            .then((response) => {
              this.$message({
                type: 'success',
                message: 'تمت عملية الحذف بنجاح',
              });
              this.handleFilter();
            })
            .catch((error) => {
              console.log(error);
            });
        })
        .catch(() => {
          this.$message({
            type: 'info',
            message: 'تم الغاء الحذف',
          });
        });
    },
    //
    valider(row) {
      ConfirmerAccomp(row.id).then(() => {
        this.$notify({
          title: 'Success',
          message: 'تم الغاء النشر بنجاح',
          type: 'success',
          duration: 2000,
        });
        this.getList();
        this.theme = false;
      });
    },
    invalider(row) {
      RefuserAccomp(row.id).then(() => {
        this.$notify({
          title: 'Success',
          message: 'نشرت بنجاح ',
          type: 'success',
          duration: 2000,
        });
        this.getList();
        this.theme = true;
      });
    },
    //
    createUser() {
      this.$refs['userForm'].validate((valid) => {
        if (valid) {
          accompanateurPackageResource
            .store(this.temp)
            .then((response) => {
              this.$message({
                message: ' تم انشاء الوصل ',
                type: 'success',
                duration: 5 * 1000,
              });
              this.resetNewUser();
              this.dialogFormVisible = false;
              this.handleFilter();
            })
            .catch((error) => {
              console.log(error);
            })
            .finally(() => {
              this.userCreating = false;
            });
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },
    updateData() {
      this.$refs['userForm'].validate((valid) => {
        if (valid) {
          const tempData = Object.assign({}, this.temp);
          accompanateurPackageResource.update(this.temp.id, tempData)
            .then((response) => {
              this.$message({
                type: 'success',
                message: 'تم تحديث المعلومات بنجاح',
                duration: 5 * 1000,
              });
              this.resetNewUser();
              this.dialogFormVisible = false;
              this.handleFilter();
            })
            .catch((error) => {
              console.log(error);
            })
            .finally(() => {
              this.getList();
              this.dialogVisible = false;
            });
        }
      });
    },
    resetNewUser() {
      this.temp = {
        id: undefined,
        user_id: '',
        package_id: '',
        etat: 1,
        role: 1,
        user: '',
        package: '',
      };
    },
    handleDownload() {
      this.downloading = true;
      import('@/vendor/Export2Excel').then((excel) => {
        const tHeader = [
          'id',

          'user_id',
          'etat',
          'package_id',
        ];
        const filterVal = [
          'id',
          'user_id',
          'etat',
          'package_id',
        ];
        const data = this.formatJson(filterVal, this.list);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: 'قائمة علاقة المرافقين بالرحلات',
        });
        this.downloading = false;
      });
    },
    formatJson(filterVal, jsonData) {
      return jsonData.map((v) => filterVal.map((j) => v[j]));
    },
    permissionKeys(permissions) {
      return permissions.map((permssion) => permssion.id);
    },
  },
};
</script>

<style lang="scss" scoped>
.edit-input {
  padding-right: 100px;
}
.cancel-btn {
  position: absolute;
  right: 15px;
  top: 10px;
}
.dialog-footer {
  text-align: left;
  padding-top: 0;
  margin-left: 150px;
}
.app-container {
  flex: 1;
  justify-content: space-between;
  font-size: 14px;
  padding-right: 8px;
  .block {
    float: left;
    min-width: 250px;
  }
  .clear-left {
    clear: left;
  }
}
</style>
