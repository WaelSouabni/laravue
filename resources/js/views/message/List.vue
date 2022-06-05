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
          :key="user.id"
          :label="user.name"
          :value="user.id"
        />
      </el-select>
      <el-button
        v-waves
        class="filter-item"
        type="primary"
        icon="el-icon-search"
        @click="handleFilter"
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

      <el-table-column align="center" label="المرسل">
        <template slot-scope="scope">
          <span>{{ scope.row.user }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="الحالة">
        <template slot-scope="scope">
          <span v-show="scope.row.etat == '1'"> مقروء </span>
          <span v-show="scope.row.etat == '0'"> غير مقروء </span>
        </template>
      </el-table-column>
      <el-table-column v-if="false" align="center" label="user_id">
        <template slot-scope="scope">
          <span>{{ scope.row.user_id }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="نص الرسالة">
        <template slot-scope="scope">
          <span>{{ scope.row.description }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="Actions" width="350">
        <template slot-scope="scope">
          <el-button
            type="primary"
            size="small"
            icon="el-icon-edit"
            @click="handleCreate(scope.row)"
          />
          <el-button
            type="danger"
            size="small"
            icon="el-icon-delete"
            @click="handleDelete(scope.row.id)"
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

    <el-dialog
      :title="'الاجابة عن الارسالية'"
      :visible.sync="dialogFormVisible"
    >
      <div v-loading="userCreating" class="form-container">
        <el-form
          ref="userForm"
          :rules="rules"
          :model="newMessage"
          label-position="top"
          label-width="150px"
          style="max-width: 1000px"
        >
          <el-row>
            <el-col :span="11">
              <el-form-item>
                <el-input v-model="newMessage.user_id" type="hidden" />
              </el-form-item>
            </el-col>
            <el-col :span="2"> - </el-col>
            <el-col :span="11">
              <el-form-item :label="$t('الباعث')" prop="name">
                <el-input v-model="newMessage.user" :disabled="true" />
              </el-form-item>
            </el-col>
          </el-row>
          <el-row>
            <el-col :span="11">
              <el-form-item :label="$t('الاجابة')" prop="reponseDescription">
                <el-input
                  v-model="newMessage.reponseDescription"
                  :rows="2"
                  type="textarea"
                />
              </el-form-item>
            </el-col>
            <el-col :span="2"> - </el-col>
            <el-col :span="11">
              <el-form-item :label="$t('نص الرسالة')" prop="description">
                <el-input
                  v-model="newMessage.description"
                  :rows="2"
                  type="textarea"
                  :disabled="true"
                />
              </el-form-item>
            </el-col>
          </el-row>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="updateData()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import waves from '@/directive/waves'; // Waves directive
import MessageResource from '@/api/message';
import axios from 'axios';
const messageResource = new MessageResource();

export default {
  name: 'MessageList',
  components: { Pagination },
  directives: { waves },
  data() {
    return {
      list: null,
      listuser: {
        id: '',
        name: '',
      },
      total: 0,
      loading: true,
      downloading: false,
      userCreating: false,
      query: {
        page: 1,
        limit: 15,
        user_id: '',
      },
      newMessage: {
        id: '',
        description: '',
        reponseDescription: '',
        user_id: '',
        user: '',
        etat: '',
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
  },
  methods: {
    async getListUser() {
      const { data } = await axios.get('http://localhost:8000/api/usersList');
      this.listuser = data.data;
      console.log(this.listuser);
    },
    async getList() {
      const { limit, page } = this.query;
      this.loading = true;
      const { data, meta } = await messageResource.list(this.query);
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
    handleCreate(row) {
      this.newMessage = Object.assign({}, row); // copy obj
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['userForm'].clearValidate();
      });
    },
    handleDelete(id) {
      this.$confirm('هل تريد حذف هذه الرسالة', 'Warning', {
        confirmButtonText: 'نعم',
        cancelButtonText: 'لا',
        type: 'warning',
      })
        .then(() => {
          messageResource
            .destroy(id)
            .then((response) => {
              this.$message({
                type: 'success',
                message: 'حذفت بنجاح',
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
            message: ' تم الغاء الحذف',
          });
        });
    },

    resetNewUser() {
      this.newMessage = {
        id: '',
        description: '',
        reponseDescription: '',
        user_id: '',
        user: '',
        etat: '',
      };
    },
    updateData() {
      this.$refs['userForm'].validate((valid) => {
        if (valid) {
          const tempData = Object.assign({}, this.newMessage);
          messageResource
            .update(this.newMessage.id, tempData)
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
    handleDownload() {
      this.downloading = true;
      import('@/vendor/Export2Excel').then((excel) => {
        const tHeader = [
          'description',
          'reponseDescription',
          'user_id',
          'etat',
        ];
        const filterVal = [
          'description',
          'reponseDescription',
          'user_id',
          'etat',
        ];
        const data = this.formatJson(filterVal, this.list);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: ' الرسائل-قائمة',
        });
        this.downloading = false;
      });
    },
    formatJson(filterVal, jsonData) {
      return jsonData.map((v) => filterVal.map((j) => v[j]));
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
