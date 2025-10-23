using Microsoft.EntityFrameworkCore;
using SmartERP.AdvancedModule.Data;
using SmartERP.AdvancedModule.DTOs;

namespace SmartERP.AdvancedModule.Services
{
    public class ReportService : IReportService
    {
        private readonly ApplicationDbContext _context;
        private readonly ILogger<ReportService> _logger;

        public ReportService(ApplicationDbContext context, ILogger<ReportService> logger)
        {
            _context = context;
            _logger = logger;
        }

        public async Task<SalesReportDto> GenerateSalesReportAsync(DateTime startDate, DateTime endDate, string groupBy)
        {
            _logger.LogInformation($"Generating sales report from {startDate} to {endDate}");

            // Mock data for now (nanti kita isi real logic)
            return await Task.FromResult(new SalesReportDto
            {
                ReportDate = DateTime.UtcNow,
                Period = groupBy,
                TotalRevenue = 15000000,
                TotalSales = 8000000,
                TotalRentals = 7000000,
                TransactionCount = 245,
                GrowthRate = 15.5m,
                TopProducts = new List<TopProductDto>
                {
                    new TopProductDto { ProductName = "Traditional Costume", QuantitySold = 45, Revenue = 5000000 },
                    new TopProductDto { ProductName = "Modern Costume", QuantitySold = 38, Revenue = 4500000 }
                },
                RevenueBreakdown = new RevenueBreakdownDto
                {
                    SalesRevenue = 8000000,
                    RentalRevenue = 7000000,
                    PaymentMethodBreakdown = new Dictionary<string, decimal>
                    {
                        { "Cash", 6000000 },
                        { "Transfer", 5000000 },
                        { "E-Wallet", 4000000 }
                    }
                }
            });
        }

        public async Task<List<ProductPerformanceDto>> GetProductPerformanceAsync(DateTime startDate, DateTime endDate)
        {
            _logger.LogInformation($"Getting product performance from {startDate} to {endDate}");

            // Mock data
            return await Task.FromResult(new List<ProductPerformanceDto>
            {
                new ProductPerformanceDto
                {
                    ProductId = 1,
                    ProductName = "Traditional Costume - Kebaya",
                    SKU = "TRAD-001",
                    TotalQuantitySold = 45,
                    TotalRevenue = 5000000,
                    TotalTransactions = 32,
                    AveragePrice = 156250,
                    PerformanceRating = "Excellent",
                    LastSoldDate = DateTime.Today.AddDays(-2)
                },
                new ProductPerformanceDto
                {
                    ProductId = 2,
                    ProductName = "Modern Costume - Party Dress",
                    SKU = "MOD-001",
                    TotalQuantitySold = 38,
                    TotalRevenue = 4500000,
                    TotalTransactions = 28,
                    AveragePrice = 160714,
                    PerformanceRating = "Good",
                    LastSoldDate = DateTime.Today.AddDays(-1)
                }
            });
        }

        public async Task<List<EmployeePerformanceDto>> GetEmployeePerformanceAsync(DateTime startDate, DateTime endDate)
        {
            _logger.LogInformation($"Getting employee performance from {startDate} to {endDate}");

            // Mock data
            return await Task.FromResult(new List<EmployeePerformanceDto>
            {
                new EmployeePerformanceDto
                {
                    EmployeeId = 1,
                    EmployeeName = "Siti Nurhaliza",
                    Position = "Sales Manager",
                    TotalTransactionsHandled = 145,
                    TotalRevenue = 8500000,
                    AttendanceRate = 96.5,
                    CustomerSatisfactionScore = 4.8,
                    PerformanceGrade = "A",
                    EvaluationPeriod = DateTime.Today
                }
            });
        }

        public async Task<DashboardSummaryDto> GetDashboardSummaryAsync()
        {
            _logger.LogInformation("Getting dashboard summary");

            // Mock data
            return await Task.FromResult(new DashboardSummaryDto
            {
                TodayRevenue = 2500000,
                MonthlyRevenue = 15000000,
                TodayTransactions = 18,
                LowStockProducts = 5,
                ActiveEmployees = 8
            });
        }
    }
}