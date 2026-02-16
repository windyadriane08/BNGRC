<?php

class DashboardController {
    public function index() {
        $dashboardService = new DashboardService();
        $data = $dashboardService->getDashboardData();
        
        Flight::render('dashboard/index', $data);
    }
}
