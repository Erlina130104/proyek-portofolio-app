namespace SmartERP.AdvancedModule.DTOs
{
    // ===== Response DTOs =====
    
    public class SalesReportDto
    {
        public DateTime ReportDate { get; set; }
        public string Period { get; set; } = string.Empty;
        public decimal TotalRevenue { get; set; }
        public decimal TotalSales { get; set; }
        public decimal TotalRentals { get; set; }
        public int TransactionCount { get; set; }
        public decimal GrowthRate { get; set; }
        public List<TopProductDto> TopProducts { get; set; } = new();
        public RevenueBreakdownDto? RevenueBreakdown { get; set; }
    }

    public class TopProductDto
    {
        public string ProductName { get; set; } = string.Empty;
        public int QuantitySold { get; set; }
        public decimal Revenue { get; set; }
    }

    public class RevenueBreakdownDto
    {
        public decimal SalesRevenue { get; set; }
        public decimal RentalRevenue { get; set; }
        public Dictionary<string, decimal> PaymentMethodBreakdown { get; set; } = new();
    }

    public class ProductPerformanceDto
    {
        public int ProductId { get; set; }
        public string ProductName { get; set; } = string.Empty;
        public string SKU { get; set; } = string.Empty;
        public int TotalQuantitySold { get; set; }
        public decimal TotalRevenue { get; set; }
        public int TotalTransactions { get; set; }
        public decimal AveragePrice { get; set; }
        public string PerformanceRating { get; set; } = string.Empty;
        public DateTime LastSoldDate { get; set; }
    }

    public class EmployeePerformanceDto
    {
        public int EmployeeId { get; set; }
        public string EmployeeName { get; set; } = string.Empty;
        public string Position { get; set; } = string.Empty;
        public int TotalTransactionsHandled { get; set; }
        public decimal TotalRevenue { get; set; }
        public double AttendanceRate { get; set; }
        public double CustomerSatisfactionScore { get; set; }
        public string PerformanceGrade { get; set; } = string.Empty;
        public DateTime EvaluationPeriod { get; set; }
    }

    public class DashboardSummaryDto
    {
        public decimal TodayRevenue { get; set; }
        public decimal MonthlyRevenue { get; set; }
        public int TodayTransactions { get; set; }
        public int LowStockProducts { get; set; }
        public int ActiveEmployees { get; set; }
    }

    // ===== Request DTOs =====
    
    public class GenerateReportRequest
    {
        public DateTime StartDate { get; set; }
        public DateTime EndDate { get; set; }
        public string ReportType { get; set; } = "sales";
        public string GroupBy { get; set; } = "daily";
    }

    // ===== Generic API Response =====
    
    public class ApiResponse<T>
    {
        public bool Success { get; set; }
        public string Message { get; set; } = string.Empty;
        public T? Data { get; set; }
        public DateTime Timestamp { get; set; } = DateTime.UtcNow;
    }
}