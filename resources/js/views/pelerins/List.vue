<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input
        v-model="query.nomArabe"
        :placeholder="$t('table.keyword')"
        style="width: 200px"
        class="filter-item"
        @keyup.enter.native="handleFilter"
      />
      <el-select
        v-model="query.package_id"
        placeholder="اختر رحلة"
        style="width: 90px"
        class="filter-item"
        @change="handleFilter"
      >
        <el-option
          v-for="item in listpackage"
          :key="item.id"
          :label="item.labelle"
          :value="item.id"
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
      <el-table-column
        :label="$t('table.id')"
        prop="id"
        sortable="custom"
        align="center"
        width="50"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('اللقب & الاسم')" width="'15%'">
        <template slot-scope="{ row }">
          <span class="link-type" @click="handleUpdate(row)">{{
            row.nomArabe + ' ' + row.prenomArabe
          }}</span>
        </template>
      </el-table-column>
      <el-table-column
        :label="$t('تاريخ_الميلاد')"
        width="'10%'"
        align="center"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.dateNaissance | parseTime('{y}-{m}-{d}') }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('اسم الرحلة')" width="'10%'" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.labelle }}</span>
        </template>
      </el-table-column>
      <el-table-column
        :label="$t('رقم_جواز_السفر')"
        width="'10%'"
        align="center"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.numeroDePassport }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('تاريخ_الاصدار')" width="'12%'">
        <template slot-scope="scope">
          <span style="color: red">{{ scope.row.dateEmission }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('تاريخ_انتهاء_الصلوحية')" width="'12%'">
        <template slot-scope="scope">
          <span style="color: red">{{ scope.row.dateExpiration }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('الحالة')" align="center" width="'5%'">
        <template slot-scope="{ row }">
          <span v-show="row.etat == '0'"> مرفوض </span>
          <span v-show="row.etat == '1'"> غير مؤكد </span>
          <span v-show="row.etat == '2'"> مؤكد </span>
        </template>
      </el-table-column>
      <el-table-column
        :label="$t('table.actions')"
        align="center"
        width="250"
        class-name="small-padding fixed-width"
      >
        <template slot-scope="{ row }">
          <el-button
            type="primary"
            size="mini"
            icon="el-icon-edit"
            @click="handleUpdate(row)"
          />
          <el-button
            v-if="row.status != 'deleted'"
            size="mini"
            icon="el-icon-delete"
            type="danger"
            @click="handleDelete(row)"
          />
          <el-button
            v-show="row.etat == '1'"
            class="filter-item"
            type="primary"
            size="mini"
            icon="el-icon-view"
            @click="invalider(row)"
          />
          <el-button
            v-show="row.etat == '1'"
            class="filter-item"
            type="primary"
            size="mini"
            icon="el-icon-close"
            @click="valider(row)"
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
        ><el-row>
           <el-col :span="12">
             <el-form-item label="اختر رحلة">
               <el-select v-model="temp.package_id" placeholder="اختر رحلة">
                 <el-option
                   v-for="item in listpackage"
                   :key="item.id"
                   :label="item.labelle"
                   :value="item.id"
                 />
               </el-select>
             </el-form-item>
           </el-col>
           <el-col :span="12">
             <el-form-item label="اختر حساب الحريف ">
               <el-select
                 v-model="temp.user_id"
                 placeholder="اختر حساب الحريف"
               >
                 <el-option
                   v-for="item in listuser"
                   :key="item.id"
                   :label="item.name"
                   :value="item.id"
                 />
               </el-select>
             </el-form-item>
           </el-col>
         </el-row>
          <el-row>
            <el-col :span="12">
              <el-form-item label="الاسم الثلاثي للحريف بالعربية">
                <el-input v-model="temp.nomArabe" />
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item label="لقب الحريف بالعربية">
                <el-input v-model="temp.prenomArabe" />
              </el-form-item>
            </el-col>
          </el-row>
          <el-form-item label="تاريخ الميلاد وجنس الحريف ">
            <el-col :span="11">
              <el-date-picker
                v-model="temp.dateNaissance"
                type="date"
                placeholder="تاريخ ميلاد الحريف"
                style="width: 100%"
              />
            </el-col>
            <el-col :span="2" class="text-center">
              <span class="text-gray-500">-</span>
            </el-col>
            <el-col :span="11">
              <el-select v-model="temp.sexe" placeholder="رجل أم امرأة">
                <el-option label="رجل" value="0" />
                <el-option label="امرأة" value="1" />
              </el-select>
            </el-col>
          </el-form-item>
          <el-row>
            <el-col :span="11">
              <el-form-item label="رقم جواز السفر">
                <el-input v-model="temp.numeroDePassport" />
              </el-form-item>
            </el-col>
            <el-col :span="2"> - </el-col>
            <el-col :span="11">
              <el-form-item label="رقم الهاتف ">
                <el-input v-model="temp.telephoneTunisien" />
              </el-form-item>
            </el-col>
          </el-row>

          <el-row>
            <el-col :span="11">
              <el-form-item label="تاريخ اصدار ">
                <el-date-picker
                  v-model="temp.dateEmission"
                  type="date"
                  placeholder="تاريخ اصدار "
                  style="width: 100%"
                />
              </el-form-item>
            </el-col>
            <el-col :span="2">  - </el-col>
            <el-col :span="11">
              <el-form-item label="تاريخ  و انتهاء جواز   السفر ">
                <el-date-picker
                  v-model="temp.dateExpiration"
                  placeholder=" انتهاء صلوحية  "
                  style="width: 100%"
                />
              </el-form-item>
            </el-col>
          </el-row>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button
            type="primary"
            @click="dialogStatus === 'create' ? createUser() : updateData()"
          >
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
import { ConfirmerPelerins, RefuserPelerins } from '@/api/pelerin';

const PelerinResource = new Resource('pelerins');

export default {
  name: 'ListeDePelerins',
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
        nomArabe: '',
        package_id: '',
      },
      roles: ['أدمين', 'مستخدم عادي'],
      nonAdminRoles: ['editor', 'user', 'visitor'],
      newUser: {},
      temp: {
        id: undefined,
        nomArabe: '',
        prenomArabe: '',
        dateNaissance: new Date(),
        sexe: '',
        telephoneTunisien: '',
        numeroDePassport: '',
        dateExpiration: new Date(),
        dateEmission: new Date(),
        user_id: '',
        createur_id: 1,
        package_id: '',
        etat: '1',
        labelle: '',
      },
      dialogFormVisible: false,
      dialogPermissionVisible: false,
      dialogPermissionLoading: false,
      dialogStatus: '',
      currentUserId: 0,
      currentUser: {
        name: '',
        permissions: [],
        rolePermissions: [],
      },
      rules: {
        nomArabe: [
          { required: true, message: 'الاسم اجباري', trigger: 'change' },
        ],
        prenomArabe: [
          { required: true, message: ' اللقب اجباري ', trigger: 'change' },
        ],
        user_id: [
          {
            required: true,
            message: 'يجب انشاء حساب حريف قبل ادخاله هنا',
            trigger: 'change',
          },
        ],
        telephoneTunisien: [
          {
            type: 'phone',
            required: true,
            message: 'رقم الهاتف اجباري ',
            trigger: 'change',
          },
        ],
        sexe: [
          { required: true, message: 'جنس الحريف اجباري', trigger: 'change' },
        ],
        package_id: [
          {
            required: true,
            message: ' وجب اختيار رحلة للحريف',
            trigger: 'change',
          },
        ],
        numeroDePassport: [
          {
            required: false,
            message: 'رقم جواز السفر',
            trigger: 'change',
          },
        ],
        dateNaissance: [
          {
            type: 'date',
            required: true,
            message: ' تاريخ الميلاد اجباري ',
            trigger: 'change',
          },
        ],
        dateExpiration: [
          {
            type: 'date',
            required: true,
            message: ' تاريخ انتهاء الصلوحية اجباري ',
            trigger: 'change',
          },
        ],
        dateEmession: [
          {
            type: 'date',
            required: true,
            message: ' تاريخ اصدار  اجباري ',
            trigger: 'change',
          },
        ],
        etat: [{ required: false, message: ' ', trigger: 'blur' }],
      },
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
      const { data } = await axios.get('http://localhost:8000/api/usersList');
      this.listuser = data.data;
      console.log(this.listuser);
    },
    valider(row) {
      ConfirmerPelerins(row.id).then(() => {
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
      RefuserPelerins(row.id).then(() => {
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
    async getListPackages() {
      const { data } = await axios.get(
        'http://localhost:8000/api/PackagesList'
      );
      this.listpackage = data.data;
      console.log(this.listpackage);
    },
    updateData() {
      this.$refs['userForm'].validate((valid) => {
        if (valid) {
          const tempData = Object.assign({}, this.temp);
          PelerinResource.update(this.temp.id, tempData)
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
    async getList() {
      const { limit, page } = this.query;
      this.loading = true;
      const { data, meta } = await PelerinResource.list(this.query);
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
      this.dialogStatus = 'create';
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['userForm'].clearValidate();
      });
    },
    handleDelete(id, name) {
      this.$confirm('هل تريد حذف هذا المعنمر ', 'تحذير', {
        confirmButtonText: 'نعم',
        cancelButtonText: 'لا',
        type: 'warning',
      })
        .then(() => {
          PelerinResource.destroy(id)
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
    createUser() {
      this.$refs['userForm'].validate((valid) => {
        if (valid) {
          PelerinResource.store(this.temp)
            .then((response) => {
              this.$message({
                message: ' تم انشاء الوصل بنجاح',
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
    resetNewUser() {
      this.temp = {
        id: undefined,
        nomArabe: '',
        prenomArabe: '',
        dateNaissance: new Date(),
        sexe: '',
        telephoneTunisien: '',
        numeroDePassport: '',
        dateExpiration: new Date(),
        dateEmission: new Date(),
        user_id: '',
        createur_id: 1,
        package_id: '',
        etat: '1',
      };
    },
    handleDownload() {
      this.downloading = true;
      import('@/vendor/Export2Excel').then((excel) => {
        const tHeader = [
          'nomArabe',
          'prenomArabe',
          'dateNaissance',
          'sexe',
          'telephoneTunisien',
          'image',
          'numeroDePassport',
          'dateExpiration',
          'dateEmission',
          'user_id',
          'createur_id',
          'etat',
          'package_id',
        ];
        const filterVal = [
          'nomArabe',
          'prenomArabe',
          'dateNaissance',
          'sexe',
          'telephoneTunisien',
          'image',
          'numeroDePassport',
          'dateExpiration',
          'dateEmission',
          'user_id',
          'createur_id',
          'etat',
          'package_id',
        ];
        const data = this.formatJson(filterVal, this.list);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: 'قائمة الخلاص',
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
.dialog-footer  {
  text-align: left;
  padding-top: 0;
  margin-left: 0;
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
