<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input
        v-model="listQuery.labelle"
        :placeholder="$t('البحث')"
        style="width: 200px"
        class="filter-item"
        @keyup.enter.native="handleFilter"
      />
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
        :loading="downloadLoading"
        class="filter-item"
        type="primary"
        icon="el-icon-download"
        @click="handleDownload"
      />
    </div>

    <el-table
      :key="tableKey"
      v-loading="listLoading"
      :data="list"
      border
      fit
      highlight-current-row
      style="width: 100%"
      @sort-change="sortChange"
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
      <el-table-column :label="$t('صورة')" width="100px" align="center">
        <template slot-scope="scope">
          <img
            v-if="scope.row.image != null"
            :src="scope.row.image"
            class="img-circle"
            alt="Cinque Terre"
            width="80"
            height="80"
          >
        </template>
      </el-table-column>
      <el-table-column :label="$t('اللقب & الاسم')" width="200px" align="center">
        <template slot-scope="{ row }">
          <span class="link-type" @click="handleUpdate(row)">{{
            row.nomArabe + ' ' + row.prenomArabe
          }}</span>
        </template>
      </el-table-column>
      <el-table-column
        :label="$t('رقم الهاتف بالخارج')"
        width="150px"
        align="center"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.telephoneEtranger }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('رقم الهاتف ')" width="150px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.telephoneTunisien }}</span>
        </template>
      </el-table-column>
      <el-table-column
        :label="$t('الحالة')"
        width="150px"
        align="center"
      >
        <template slot-scope="scope">
          <span v-show="scope.row.etat == '1'"> منشور </span>
          <span v-show="scope.row.etat == '0'"> غير منشور </span>
        </template>
      </el-table-column>

      <el-table-column
        :label="$t('table.actions')"
        align="center"
        width="300"
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
            v-if="row.etat == 1"
            v-waves
            class="filter-item"
            type="primary"
            size="mini"
            icon="el-icon-close"
            @click="masquer(row)"
          />
          <el-button
            v-else
            v-waves
            class="filter-item"
            type="primary"
            size="mini"
            icon="el-icon-view"
            @click="publier(row)"
          />
          <el-button
            v-if="row.status != 'deleted'"
            size="mini"
            icon="el-icon-delete"
            type="danger"
            @click="handleDelete(row)"
          />
        </template>
      </el-table-column>
    </el-table>

    <pagination
      v-show="total > 0"
      :total="total"
      :page.sync="listQuery.page"
      :limit.sync="listQuery.limit"
      @pagination="getList"
    />

    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogFormVisible">
      <el-form
        ref="dataForm"
        :rules="rules"
        enctype="multipart/form-data"
        :model="temp"
        label-position="top"
        label-width="150px"
        style="max-width: 1000px"
      >
        <el-row>
          <el-col :span="11">
            <el-form-item label="اختر حساب الحريف ">
              <el-select v-model="temp.user_id" placeholder="اختر حساب الحريف">
                <el-option
                  v-for="item in listuser"
                  :key="item.id"
                  :label="item.name"
                  :value="item.id"
                />
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="2"> - </el-col>
          <el-col :span="11">
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
        </el-row>
        <el-row>
          <el-col :span="11">
            <el-form-item label="لقب الحريف بالعربية">
              <el-input v-model="temp.prenomArabe" />
            </el-form-item>
          </el-col>
          <el-col :span="2"> - </el-col>
          <el-col :span="11">
            <el-form-item label="الاسم الثلاثي للحريف بالعربية">
              <el-input v-model="temp.nomArabe" />
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="11">
            <el-form-item label="الجنس">
              <el-select v-model="temp.sexe" placeholder="رجل أم امرأة">
                <el-option label="رجل" value="0" />
                <el-option label="امرأة" value="1" />
              </el-select>
            </el-form-item>
            <el-form-item label="رقم الهاتف بالخارج">
              <el-input v-model="temp.telephoneEtranger" />
            </el-form-item>
          </el-col>
          <el-col :span="2"> - </el-col>
          <el-col :span="11">
            <el-form-item label="رقم الهاتف ">
              <el-input v-model="temp.telephoneTunisien" />
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item
          label="الصورة"
        ><br>
          <dropzone
            id="myVueDropzone"
            url="https://httpbin.org/post"
            @dropzone-removedFile="dropzoneR"
            @dropzone-success="dropzoneS"
          />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false">
          {{ $t('table.cancel') }}
        </el-button>
        <el-button
          type="primary"
          @click="dialogStatus === 'create' ? createData() : updateData()"
        >
          {{ $t('table.confirm') }}
        </el-button>
      </div>
    </el-dialog>
    <el-dialog :visible.sync="dialogPvVisible" title="Reading statistics">
      <el-table
        :data="pvData"
        border
        fit
        highlight-current-row
        style="width: 100%"
      >
        <el-table-column prop="key" label="Channel" />
        <el-table-column prop="pv" label="Pv" />
      </el-table>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" @click="dialogPvVisible = false">{{
          $t('table.confirm')
        }}</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { fetchPv, masquerAccomp, publierAccomp } from '@/api/accompagnateur';
import waves from '@/directive/waves'; // Waves directive
import { parseTime } from '@/utils';
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import Resource from '@/api/resource';
import Dropzone from '@/components/Dropzone';
import axios from 'axios';
const AcompgnateurResource = new Resource('accompgnateurs');

export default {
  name: 'ListeDePelerins',
  components: { Pagination, Dropzone },
  directives: { waves },
  filters: {
    statusFilter(status) {
      const statusMap = {
        published: 'success',
        draft: 'info',
        deleted: 'danger',
      };
      return statusMap[status];
    },
  },
  data() {
    return {
      tableKey: 0,
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
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 20,
        labelle: undefined,
        sort: '+id',
      },
      temp: {
        id: undefined,
        nomArabe: '',
        prenomArabe: '',
        sexe: '',
        telephoneTunisien: '',
        telephoneEtranger: '',
        user_id: '',
        image: '',
        package_id: '',
      },
      dialogFormVisible: false,
      dialogStatus: '',
      textMap: {
        update: 'تعديل',
        create: 'انشاء',
      },
      dialogPvVisible: false,
      pvData: [],
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
      },
      downloadLoading: false,
    };
  },
  watch: {},
  created() {
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
    async getListPackages() {
      const { data } = await axios.get(
        'http://localhost:8000/api/PackagesList'
      );
      this.listpackage = data.data;
      console.log(this.listpackage);
    },
    publier(row) {
      publierAccomp(row.id).then(() => {
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
    masquer(row) {
      masquerAccomp(row.id).then(() => {
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
    async getList() {
      this.listLoading = true;
      const { data } = await AcompgnateurResource.list(this.query);
      console.log(data);
      this.list = data;
      this.total = data.length;

      // Just to simulate the time of the request
      this.listLoading = false;
    },
    handleFilter() {
      this.listQuery.page = 1;
      this.getList();
    },
    handleModifyStatus(row, status) {
      this.$message({
        message: 'Successful operation',
        type: 'success',
      });
      row.status = status;
    },
    sortChange(data) {
      const { prop, order } = data;
      if (prop === 'id') {
        this.sortByID(order);
      }
    },
    sortByID(order) {
      if (order === 'ascending') {
        this.listQuery.sort = '+id';
      } else {
        this.listQuery.sort = '-id';
      }
      this.handleFilter();
    },
    resetTemp() {
      this.temp = {
        id: undefined,
        nomArabe: '',
        prenomArabe: '',
        sexe: '',
        telephoneTunisien: '',
        telephoneEtranger: '',
        image: '',
        user_id: '',
        package_id: '',
      };
    },
    handleCreate() {
      this.resetTemp();
      this.dialogStatus = 'create';
      this.temp.image = '';
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['dataForm'].clearValidate();
      });
    },
    createData() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          this.temp.id = parseInt(Math.random() * 100) + 1024; // mock a id
          this.temp.author = 'laravue';
          AcompgnateurResource.store(this.temp)
            .then((response) => {
              this.$message({
                message: 'تمت الاضافة بنجاح',
                type: 'success',
                duration: 5 * 1000,
              });
              this.resetTemp();
              this.dialogFormVisible = false;
              this.getList();
            })
            .catch((error) => {
              console.log(error);
            });
          this.dialogVisible = false;
          this.getList();
        }
      });
    },
    handleUpdate(row) {
      this.temp = Object.assign({}, row); // copy obj
      this.dialogStatus = 'update';
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['dataForm'].clearValidate();
      });
    },
    updateData() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const tempData = Object.assign({}, this.temp);
          AcompgnateurResource.update(this.temp.id, tempData)
            .then((response) => {
              this.$message({
                type: 'success',
                message: 'تم تحديث المعلومات بنجاح',
                duration: 5 * 1000,
              });
              this.resetTemp();
              this.dialogFormVisible = false;
              this.getList();
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
    handleDelete(row) {
      this.$confirm('هل انت متؤكد من الحذف', 'Warning', {
        confirmButtonText: 'نعم',
        cancelButtonText: 'لا',
        type: 'warning',
      })
        .then(() => {
          AcompgnateurResource.destroy(row.id)
            .then((response) => {
              this.$message({
                type: 'success',
                message: 'اكتمل الحذف',
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
            message: 'تم إلغاء الحذف',
          });
        });
    },
    handleFetchPv(pv) {
      fetchPv(pv).then((response) => {
        this.pvData = response.data.pvData;
        this.dialogPvVisible = true;
      });
    },
    dropzoneS(file) {
      this.$message({ message: 'تم الاضافة بنجاح', type: 'success' });
      this.temp.image = file;
    },
    dropzoneR(file) {
      this.$message({ message: 'تم الحذف بنجاح ', type: 'success' });
      this.temp.image = [];
    },
    handleDownload() {
      this.downloadLoading = true;
      import('@/vendor/Export2Excel').then((excel) => {
        const tHeader = [
          'nomArabe',
          'prenomArabe',
          'sexe',
          'telephoneTunisien',
          'image',
          'telephoneEtranger',
          'user_id',
          'package_id',
        ];
        const filterVal = [
          'nomArabe',
          'prenomArabe',
          'sexe',
          'telephoneTunisien',
          'image',
          'telephoneEtranger',
          'user_id',
          'package_id',
        ];
        const data = this.formatJson(filterVal, this.list);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: 'table-list',
        });
        this.downloadLoading = false;
      });
    },
    formatJson(filterVal, jsonData) {
      return jsonData.map((v) =>
        filterVal.map((j) => {
          if (j === 'dateDepart') {
            return parseTime(v[j]);
          } else {
            return v[j];
          }
        })
      );
    },
  },
};
</script>
