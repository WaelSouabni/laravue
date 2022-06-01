<template>
  <div class="app-container">
    <el-form v-if="packages" :model="packages">
      <el-row :gutter="20">
        <el-col :span="6">
          <el-card v-if="packages.labelle">
            <div class="user-profile">
              <div class="user-avatar box-center">
                <pan-thumb
                  v-if="packages.image != null"
                  :image="packages.image"
                  :height="'100px'"
                  :width="'100px'"
                  :hoverable="false"
                />
              </div>
              <div class="box-center">
                <div class="user-name text-center">
                  {{ packages.labelle }}
                </div>
                <div class="user-role text-center text-muted">
                  {{ packages.dateDepart }}: تاريخ الرحلة
                </div>
                <div class="user-role text-center text-muted">
                  {{ packages.prix }}: ثمن الرحلة
                </div>
              </div>
              <div class="box-social">
                <el-table :show-header="false">
                  <el-table-column prop="name" label="Name" />
                  <el-table-column label="Count" align="left" width="100">
                    <template>
                      {{ packages.NombrePlace }}:عدد المقاعد
                    </template>
                  </el-table-column>
                </el-table>
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :span="18">
          <div class="user-follow">
            <el-card v-if="packages.labelle">
              <el-table
                v-loading="loading"
                :data="packages.pelerins"
                border
                fit
                highlight-current-row
                style="width: 100%"
              >
                <el-table-column align="center" label="لاسم و اللقب">
                  <template slot-scope="scope">
                    <span>{{
                      scope.row.prenomArabe + ' ' + scope.row.nomArabe
                    }}</span>
                  </template>
                </el-table-column>

                <el-table-column align="center" label="رقم جواز السفر">
                  <template slot-scope="scope">
                    <span>{{ scope.row.numeroDePassport }}</span>
                  </template>
                </el-table-column>
                <el-table-column align="center" label="الحالة">
                  <template slot-scope="{ row }">
                    <span v-show="row.etat == '0'"> مرفوض </span>
                    <span v-show="row.etat == '1'"> غير مؤكد </span>
                    <span v-show="row.etat == '2'"> مؤكد </span>
                  </template>
                </el-table-column>
              </el-table>
            </el-card>
          </div>
        </el-col>
      </el-row>
    </el-form>
  </div>
</template>

<script>
import axios from 'axios';
import PanThumb from '@/components/PanThumb';
export default {
  name: 'EditUser',
  components: { PanThumb },
  data() {
    return {
      user: {},
      packages: {
        NombreAcc: 0,
        NombrePlace: 0,
        NombrePlaceRestant: 0,
        dateDepart: '',
        description: '',
        id: 0,
        image: '',
        labelle: '',
        pelerins: [],
      },
    };
  },
  watch: {
    $route: 'getUser',
  },
  created() {
    const id = this.$route.params && this.$route.params.id;
    this.getUser(id);
    console.log(this.packages);
  },
  methods: {
    async getUser(id) {
      const { data } = await axios.get(
        'http://127.0.0.1:8000/api/listePelerins/' + id
      );
      this.packages.labelle = data.data['labelle'];
      this.packages.prix = data.data['prix'];
      this.packages.NombreAcc = data.data['NombreAcc'];
      this.packages.description = data.data['description'];
      this.packages.NombrePlace = data.data['NombrePlace'];
      this.packages.dateDepart = data.data['dateDepart'];
      this.packages.NombrePlaceRestant = data.data['NombrePlaceRestant'];
      this.packages.id = data.data['id'];
      this.packages.image = data.data['image'];
      this.packages.pelerins = data.data['pelerins'];
    },
  },
};
</script>
