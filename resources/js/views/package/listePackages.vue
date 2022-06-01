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
      <el-table-column :label="$t('عنوان')" min-width="80px">
        <template slot-scope="{ row }">
          <router-link :to="'/packages/' + row.id">
            <span class="link-type">{{ row.labelle }}</span>
          </router-link>
        </template>
      </el-table-column>
      <el-table-column :label="$t('تاريخ الرحلة')" width="110px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.dateDepart | parseTime('{y}-{m}-{d}') }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('عدد المرافقين')" width="80px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.NombreAcc }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t(' المقاعدعدد')" width="80px" align="center">
        <template slot-scope="scope">
          <span style="color: red" align="center">{{
            scope.row.NombrePlace
          }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('عدد المقاعد المتبقية')" width="70px">
        <template slot-scope="scope">
          <span style="color: red">{{ scope.row.NombrePlaceRestant }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('الحالة')" align="center" width="70px">
        <template slot-scope="{ row }">
          <span v-if="row.etat == 1">منشورة</span>
          <span v-else>غير منشورة</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('الثمن')" class-name="status-col" width="70">
        <template slot-scope="{ row }">
          <el-tag :type="row.status | statusFilter">
            {{ row.prix }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column
        :label="$t('table.actions')"
        align="center"
        width="230"
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
            v-if="row.etat == '1'"
            v-waves
            class="filter-item"
            size="mini"
            type="primary"
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
      ><el-row>
         <el-col :span="11">
           <el-form-item :label="$t('العنوان')" prop="labelle" class="col-6">
             <el-input v-model="temp.labelle" placeholder="حدد عنوان الرحلة" />
           </el-form-item>
         </el-col>
         <el-col :span="2"> - </el-col>
         <el-col :span="11">
           <el-form-item :label="$t('وصف الرحلة')" prop="description">
             <el-input
               v-model="temp.description"
               :autosize="{ minRows: 2, maxRows: 4 }"
               type="textarea"
               placeholder="حدد وصف الرحلة"
             />
           </el-form-item>
         </el-col></el-row>
        <el-row>
          <el-col :span="11">
            <el-form-item :label="$t('الثمن')" prop="prix">
              <el-input
                v-model="temp.prix"
                type="number"
                min="100"
                placeholder="حدد ثمن الرحلة"
              />
            </el-form-item>
          </el-col>
          <el-col :span="2"> - </el-col>
          <el-col :span="11">
            <el-form-item :label="$t('تاريخ الرحلة')" prop="dateDepart">
              <el-date-picker
                v-model="temp.dateDepart"
                type="datetime"
                placeholder="حدد تاريخ الرحلة"
              />
            </el-form-item>
          </el-col>
        </el-row>

        <el-row>
          <el-col :span="11">
            <el-form-item :label="$t('عدد_المرافقين')" prop="NombreAcc">
              <el-select
                v-model="temp.NombreAcc"
                class="filter-item"
                placeholder="حدد عدد المرافقين"
              >
                <el-option :label="1" :value="1" />
                <el-option :label="2" :value="2" />
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="2"> - </el-col>
          <el-col :span="11">
            <el-form-item :label="$t('عدد المعتمرين')" prop="NombrePlace">
              <el-input
                v-model="temp.NombrePlace"
                type="number"
                min="1"
                placeholder="Please pick a date"
              />
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item label="الصورة"><br>
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
import {
  fetchList,
  fetchPv,
  deletePackage,
  masquerPackage,
  publierPackage,
} from '@/api/package';
import waves from '@/directive/waves'; // Waves directive
import { parseTime } from '@/utils';
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import Dropzone from '@/components/Dropzone';
import Resource from '@/api/resource';
const PackageResource = new Resource('packages');

export default {
  name: 'ListeDePckage',
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
        labelle: '',
        description: '',
        dateDepart: new Date(),
        NombrePlace: '',
        NombreAcc: '',
        prix: '',
        image: '',
        etat: '0',
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
        labelle: [
          { required: true, message: 'عنوان الرحلة اجباري', trigger: 'change' },
        ],
        description: [
          { required: true, message: 'وصف الرحلة اجباري', trigger: 'change' },
        ],
        NombrePlace: [
          { required: true, message: 'عدد المقاعد اجباري', trigger: 'change' },
        ],
        NombreAcc: [
          {
            required: true,
            message: 'عدد المقاعد  المرافقين اجباري',
            trigger: 'change',
          },
        ],
        prix: [
          { required: true, message: 'ثمن الرحلة اجباري', trigger: 'change' },
        ],
        image: [
          {
            required: false,
            message: 'صورة الرحلة اختيارية',
            trigger: 'change',
          },
        ],
        dateDepart: [
          {
            type: 'date',
            required: true,
            message: ' تاريخ الرحلة اجباري ',
            trigger: 'change',
          },
        ],
        etat: [{ required: false, message: ' ', trigger: 'blur' }],
      },
      downloadLoading: false,
    };
  },
  watch: {},
  created() {
    this.getList();
  },
  methods: {
    publier(row) {
      console.log(row.id);
      publierPackage(row.id).then(() => {
        this.$notify({
          title: 'Success',
          message: 'تم الغاء النشر بنجاح',
          type: 'success',
          duration: 2000,
        });
        this.getList();
      });
    },
    masquer(row) {
      masquerPackage(row.id).then(() => {
        this.$notify({
          title: 'Success',
          message: 'نشرت بنجاح ',
          type: 'success',
          duration: 2000,
        });
        this.getList();
      });
    },
    //
    goToDetaill(row) {
      this.$router.push('/detail/' + row.id);
    },
    async getList() {
      this.listLoading = true;
      const { data } = await fetchList(this.listQuery);
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
        labelle: '',
        description: '',
        dateDepart: new Date(),
        NombrePlace: '',
        NombreAcc: '',
        prix: '',
        image: '',
        etat: '0',
      };
    },
    handleCreate() {
      this.resetTemp();
      this.dialogStatus = 'create';
      this.temp.image = [];
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
          PackageResource.store(this.temp)
            .then((response) => {
              this.$message({
                message: 'تمت الاضافة بنجاح',
                type: 'success',
                duration: 5 * 1000,
              });
              this.resetTemp();
              this.dialogFormVisible = false;
              this.getList();
              this.handleFilter();
            })
            .catch((error) => {
              console.log(error);
            });
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
          PackageResource.update(this.temp.id, tempData)
            .then((response) => {
              this.$message({
                type: 'success',
                message: 'تم انشاء الحساب ',
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
      deletePackage(row.id).then(() => {
        this.$notify({
          title: 'Success',
          message: 'حذف بنجاح',
          type: 'success',
          duration: 2000,
        });
        const index = this.list.indexOf(row);
        this.list.splice(index, 1);
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
          'labelle',
          'dateDepart',
          'NombrePlace',
          'NombrePlaceRestant',
          'NombreAcc',
          'description',
        ];
        const filterVal = [
          'labelle',
          'dateDepart',
          'NombrePlace',
          'NombrePlaceRestant',
          'NombreAcc',
          'description',
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
