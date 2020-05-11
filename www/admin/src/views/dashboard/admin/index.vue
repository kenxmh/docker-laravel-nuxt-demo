<template>
  <div class="dashboard-editor-container">

    <!-- <panel-group/> -->
    <stat-chart/>
  </div>
</template>

<script>
// import PanelGroup from './components/PanelGroup'
import StatChart from './components/StatChart'

export default {
  name: 'DashboardAdmin',
  components: {
    // PanelGroup,
    StatChart
  },
}
</script>

<style lang="scss" scoped>
.dashboard-editor-container {
  padding: 32px;
  // background-color: rgb(240, 242, 245);
  position: relative;

  .github-corner {
    position: absolute;
    top: 0px;
    border: 0;
    right: 0;
  }

  .chart-wrapper {
    background: #fff;
    padding: 16px 16px 0;
    margin-bottom: 32px;
  }
}

@media (max-width:1024px) {
  .chart-wrapper {
    padding: 8px;
  }
}
</style>
