<template>
  <el-row :gutter="40" class="panel-group">
    <el-col :xs="12" :sm="12" :md="12" :lg="12" class="card-panel-col">
      <el-card class="box-card" v-loading="loading">
        <el-tabs v-model="activeName" @tab-click="handleClick">
          <el-tab-pane label="日统计" name="day"></el-tab-pane>
          <el-tab-pane label="周统计" name="week"></el-tab-pane>
        </el-tabs>
        <ve-line :data="chartData" :extend="extend"></ve-line>
      </el-card>
    </el-col>
  </el-row>
</template>

<script>
import VCharts from "v-charts";
import VeLine from "v-charts/lib/line.common";
import { getStats } from "@/api/stat";

export default {
  components: {
    VeLine
  },
  data() {
    this.extend = {
      series: {
        label: {
          normal: {
            show: true
          }
        }
      }
    };
    return {
      loading: true,
      activeName: "day",
      chartData: {},
      statData: {}
      // statData: {
      //   week: {
      //     columns: ["日期", "评论"],
      //     rows: [
      //       { 日期: "2020-05-01", 评论: 19810 },
      //       { 日期: "2020-05-02", 评论: 4398 },
      //       { 日期: "2020-05-03", 评论: 52910 }
      //     ]
      //   },
      //   month: {
      //     columns: ["日期", "评论"],
      //     rows: [
      //       { 日期: "2018-02", 评论: 810 },
      //       { 日期: "2018-03", 评论: 398 },
      //       { 日期: "2018-04", 评论: 910 }
      //     ]
      //   }
      // }
    };
  },
  created() {
    this.getStats();
  },
  methods: {
    async getStats() {
      const res = await getStats();
      this.statData = res;
      this.chartData = this.statData.day || {}
      this.loading = false
    },
    handleClick(tab, event) {
      this.chartData = this.statData[tab.name] || {};
    }
  }
};
</script>

<style lang="scss" scoped>
</style>
