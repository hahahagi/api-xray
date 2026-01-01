<template>
  <div class="report-dashboard">
    <!-- Summary Row -->
    <div class="row g-4 mb-4">
      <!-- Today's Production -->
      <div class="col-md-4">
        <div class="card summary-card border-0 shadow-sm h-100">
          <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div>
                <h6 class="text-uppercase text-muted small fw-bold mb-1">
                  Today's Production
                </h6>
                <h2 class="display-6 fw-bold text-primary mb-0">
                  {{ summary.today.total }}
                </h2>
              </div>
              <div
                class="icon-box bg-primary-subtle text-primary rounded-circle p-3"
              >
                <i class="bi bi-calendar-check fs-4"></i>
              </div>
            </div>
            <div
              class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top"
            >
              <div class="d-flex align-items-center gap-2">
                <span
                  class="badge bg-success-subtle text-success rounded-pill px-3"
                >
                  <i class="bi bi-check-circle me-1"></i>
                  {{ summary.today.ok }} OK
                </span>
                <span
                  class="badge bg-danger-subtle text-danger rounded-pill px-3"
                >
                  <i class="bi bi-x-circle me-1"></i> {{ summary.today.ng }} NG
                </span>
              </div>
              <div class="text-end">
                <small class="text-muted d-block">Quality Rate</small>
                <span class="fw-bold text-dark"
                  >{{ summary.today.quality_rate }}%</span
                >
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Yesterday -->
      <div class="col-md-4">
        <div class="card summary-card border-0 shadow-sm h-100">
          <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div>
                <h6 class="text-uppercase text-muted small fw-bold mb-1">
                  Yesterday
                </h6>
                <h2 class="display-6 fw-bold text-secondary mb-0">
                  {{ summary.yesterday.total }}
                </h2>
              </div>
              <div
                class="icon-box bg-secondary-subtle text-secondary rounded-circle p-3"
              >
                <i class="bi bi-calendar-minus fs-4"></i>
              </div>
            </div>
            <div
              class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top"
            >
              <div class="d-flex align-items-center gap-2">
                <span
                  class="badge bg-success-subtle text-success rounded-pill px-3"
                >
                  <i class="bi bi-check-circle me-1"></i>
                  {{ summary.yesterday.ok }} OK
                </span>
                <span
                  class="badge bg-danger-subtle text-danger rounded-pill px-3"
                >
                  <i class="bi bi-x-circle me-1"></i>
                  {{ summary.yesterday.ng }} NG
                </span>
              </div>
              <div class="text-end">
                <small class="text-muted d-block">Quality Rate</small>
                <span class="fw-bold text-dark"
                  >{{ summary.yesterday.quality_rate }}%</span
                >
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- This Month -->
      <div class="col-md-4">
        <div class="card summary-card border-0 shadow-sm h-100">
          <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div>
                <h6 class="text-uppercase text-muted small fw-bold mb-1">
                  This Month
                </h6>
                <h2 class="display-6 fw-bold text-info mb-0">
                  {{ summary.month.total }}
                </h2>
              </div>
              <div class="icon-box bg-info-subtle text-info rounded-circle p-3">
                <i class="bi bi-calendar-month fs-4"></i>
              </div>
            </div>
            <div
              class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top"
            >
              <div class="d-flex align-items-center gap-2">
                <span
                  class="badge bg-success-subtle text-success rounded-pill px-3"
                >
                  <i class="bi bi-check-circle me-1"></i>
                  {{ summary.month.ok }} OK
                </span>
                <span
                  class="badge bg-danger-subtle text-danger rounded-pill px-3"
                >
                  <i class="bi bi-x-circle me-1"></i> {{ summary.month.ng }} NG
                </span>
              </div>
              <div class="text-end">
                <small class="text-muted d-block">Quality Rate</small>
                <span class="fw-bold text-dark"
                  >{{ summary.month.quality_rate }}%</span
                >
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions & Tabs -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div
            class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3"
          >
            <ul
              class="nav nav-pills card-header-pills flex-nowrap overflow-auto pb-2 pb-lg-0 w-100 w-lg-auto"
              style="scrollbar-width: none; -ms-overflow-style: none"
            >
              <li class="nav-item">
                <button
                  :class="`nav-link text-nowrap px-4 ${
                    activeTab === 'production' ? 'active' : ''
                  }`"
                  @click="activeTab = 'production'"
                >
                  <i class="bi bi-graph-up me-2"></i>Production Report
                </button>
              </li>
              <li class="nav-item">
                <button
                  :class="`nav-link text-nowrap px-4 ${
                    activeTab === 'historical' ? 'active' : ''
                  }`"
                  @click="activeTab = 'historical'"
                >
                  <i class="bi bi-clock-history me-2"></i>Historical Log
                </button>
              </li>
              <li class="nav-item">
                <button
                  :class="`nav-link text-nowrap px-4 ${
                    activeTab === 'analytics' ? 'active' : ''
                  }`"
                  @click="activeTab = 'analytics'"
                >
                  <i class="bi bi-bar-chart me-2"></i>Analytics
                </button>
              </li>
            </ul>

            <div class="d-flex gap-2 w-100 w-lg-auto">
              <button
                @click="exportAllData"
                class="btn btn-outline-success d-flex align-items-center justify-content-center gap-2 flex-fill flex-lg-grow-0"
              >
                <i class="bi bi-download"></i>
                <span class="">Export Data</span>
              </button>
              <button
                @click="showDateRangePicker = true"
                class="btn btn-outline-primary d-flex align-items-center justify-content-center gap-2 flex-fill flex-lg-grow-0"
              >
                <i class="bi bi-calendar-range"></i>
                <span class="">Custom Range</span>
              </button>
            </div>
          </div>

          <div class="card-body p-4">
            <ProductionReport v-if="activeTab === 'production'" />
            <HistoricalLog v-if="activeTab === 'historical'" />
            <ReportAnalytics v-if="activeTab === 'analytics'" />
          </div>
        </div>
      </div>
    </div>

    <!-- Date Range Picker Modal -->
    <div v-if="showDateRangePicker" class="modal-backdrop fade show"></div>
    <div
      v-if="showDateRangePicker"
      class="modal fade show d-block"
      tabindex="-1"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
          <div class="modal-header border-bottom-0 pb-0">
            <h5 class="modal-title fw-bold">Select Date Range</h5>
            <button
              @click="showDateRangePicker = false"
              class="btn-close"
            ></button>
          </div>
          <div class="modal-body pt-4">
            <div class="mb-3">
              <label class="form-label text-muted small fw-bold text-uppercase"
                >Start Date</label
              >
              <input
                type="date"
                v-model="customDateRange.start"
                class="form-control form-control-lg"
              />
            </div>
            <div class="mb-3">
              <label class="form-label text-muted small fw-bold text-uppercase"
                >End Date</label
              >
              <input
                type="date"
                v-model="customDateRange.end"
                class="form-control form-control-lg"
              />
            </div>
          </div>
          <div class="modal-footer border-top-0 pt-0 pb-4">
            <button
              @click="showDateRangePicker = false"
              class="btn btn-light btn-lg px-4"
            >
              Cancel
            </button>
            <button
              @click="applyCustomRange"
              class="btn btn-primary btn-lg px-4"
            >
              Apply Filter
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import ProductionReport from "./ProductionReport.vue";
import HistoricalLog from "./HistoricalLog.vue";
import ReportAnalytics from "./ReportAnalytics.vue";
import apiClient from "../../api";

const activeTab = ref("production");
const showDateRangePicker = ref(false);

const summary = ref({
  today: { total: 0, ok: 0, ng: 0, quality_rate: 0 },
  yesterday: { total: 0, ok: 0, ng: 0 },
  month: { total: 0, ok: 0, ng: 0 },
});

const customDateRange = ref({
  start: new Date().toISOString().split("T")[0],
  end: new Date().toISOString().split("T")[0],
});

const fetchSummary = async () => {
  try {
    const response = await apiClient.get("", {
      params: { mode: "get_dashboard_summary" },
    });

    if (response.data.status === "success") {
      summary.value = response.data;
    }
  } catch (error) {
    console.error("Error fetching summary:", error);
  }
};

const exportAllData = async () => {
  try {
    // Export all data from different endpoints
    const [logRes, prodRes] = await Promise.all([
      apiClient.get("", {
        params: {
          mode: "get_historical_log",
          date: new Date().toISOString().split("T")[0],
        },
      }),
      apiClient.get("", {
        params: {
          mode: "get_production_report",
          start_date: new Date(
            new Date().getFullYear(),
            new Date().getMonth(),
            1
          )
            .toISOString()
            .split("T")[0],
          end_date: new Date().toISOString().split("T")[0],
        },
      }),
    ]);

    // Combine data and export
    const allData = {
      logs: logRes.data.logs || [],
      production: prodRes.data.reports || [],
    };

    // Export as JSON file
    const dataStr = JSON.stringify(allData, null, 2);
    const dataUri =
      "data:application/json;charset=utf-8," + encodeURIComponent(dataStr);
    const exportFileDefaultName = `xray_data_export_${
      new Date().toISOString().split("T")[0]
    }.json`;

    const linkElement = document.createElement("a");
    linkElement.setAttribute("href", dataUri);
    linkElement.setAttribute("download", exportFileDefaultName);
    linkElement.click();
  } catch (error) {
    console.error("Error exporting data:", error);
    alert("Failed to export data");
  }
};

const applyCustomRange = () => {
  showDateRangePicker.value = false;
  // Trigger data refresh with custom range
  // This would be handled by child components through props/events
};

onMounted(() => {
  fetchSummary();
});
</script>

<style scoped>
.report-dashboard {
  padding-bottom: 2rem;
}

.summary-card {
  transition: all 0.3s ease;
  background: #fff;
}

.summary-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
}

.icon-box {
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.nav-pills .nav-link {
  color: #6c757d;
  font-weight: 500;
  transition: all 0.2s;
  border-radius: 0.5rem;
}

.nav-pills .nav-link:hover {
  background-color: #f8f9fa;
  color: #0d6efd;
}

.nav-pills .nav-link.active {
  background-color: #0d6efd;
  color: #fff;
  box-shadow: 0 2px 4px rgba(13, 110, 253, 0.2);
}

.modal-backdrop {
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 1040;
}

.modal {
  z-index: 1050;
}

/* Custom scrollbar for the card body if needed */
.card-body {
  scrollbar-width: thin;
  scrollbar-color: #dee2e6 #fff;
}
</style>
