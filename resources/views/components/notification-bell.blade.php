@if(auth()->check())
<div class="notification-bell-container relative">
    <button id="notification-bell" class="relative p-2 text-gray-600 hover:text-gray-800 transition-colors" title="Thông báo">
        <i class="fas fa-bell text-xl"></i>
        <span id="notification-badge" class="notification-badge absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden">
            0
        </span>
    </button>

    <!-- Notification Dropdown -->
    <div id="notification-dropdown" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg hidden z-50 max-h-96 overflow-y-auto">
        <div class="p-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">Thông báo</h3>
        </div>
        <div id="notification-list" class="divide-y divide-gray-200">
            <div class="p-4 text-center text-gray-500">
                Đang tải...
            </div>
        </div>
        <div class="p-4 border-t border-gray-200 text-center">
            <a href="#" class="text-blue-600 hover:text-blue-800 text-sm">Xem tất cả</a>
        </div>
    </div>
</div>

<style>
    .notification-bell-container {
        position: relative;
    }

    .notification-badge {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.7;
        }
    }

    .notification-item {
        padding: 12px 16px;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .notification-item:hover {
        background-color: #f9fafb;
    }

    .notification-item.unread {
        background-color: #eff6ff;
    }

    .notification-action {
        display: flex;
        gap: 8px;
        margin-top: 8px;
    }

    .notification-action button {
        flex: 1;
        padding: 6px 12px;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        font-size: 12px;
        transition: all 0.2s;
    }

    .send-email-btn {
        background-color: #667eea;
        color: white;
    }

    .send-email-btn:hover {
        background-color: #5568d3;
    }

    .mark-read-btn {
        background-color: #e9ecef;
        color: #333;
    }

    .mark-read-btn:hover {
        background-color: #dee2e6;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notificationBell = document.getElementById('notification-bell');
        const notificationDropdown = document.getElementById('notification-dropdown');
        const notificationList = document.getElementById('notification-list');
        const notificationBadge = document.getElementById('notification-badge');

        // Toggle dropdown
        notificationBell.addEventListener('click', function(e) {
            e.preventDefault();
            notificationDropdown.classList.toggle('hidden');
            loadNotifications();
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.notification-bell-container')) {
                notificationDropdown.classList.add('hidden');
            }
        });

        // Load notifications
        function loadNotifications() {
            fetch('/api/notifications')
                .then(response => response.json())
                .then(data => {
                    displayNotifications(data.notifications.data);
                    updateBadge(data.unread_count);
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                    notificationList.innerHTML = '<div class="p-4 text-center text-red-500">Lỗi khi tải thông báo</div>';
                });
        }

        // Display notifications
        function displayNotifications(notifications) {
            if (notifications.length === 0) {
                notificationList.innerHTML = '<div class="p-4 text-center text-gray-500">Không có thông báo nào</div>';
                return;
            }

            notificationList.innerHTML = notifications.map(notif => `
            <div class="notification-item ${notif.read_at ? '' : 'unread'}">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">${notif.data.message}</p>
                        <p class="text-xs text-gray-500 mt-1">${new Date(notif.created_at).toLocaleString('vi-VN')}</p>
                    </div>
                    ${!notif.read_at ? '<span class="inline-block w-2 h-2 bg-blue-600 rounded-full mt-1"></span>' : ''}
                </div>
                <div class="notification-action">
                    <button class="mark-read-btn" onclick="markNotificationAsRead('${notif.id}')">
                        <i class="fas fa-check mr-1"></i> Đánh dấu đã đọc
                    </button>
                </div>
            </div>
        `).join('');
        }

        // Update badge
        function updateBadge(count) {
            if (count > 0) {
                notificationBadge.textContent = count;
                notificationBadge.classList.remove('hidden');
            } else {
                notificationBadge.classList.add('hidden');
            }
        }

        // Refresh notifications every 30 seconds
        setInterval(loadNotifications, 30000);

        // Load on page load
        loadNotifications();
    });

    // Send deadline email to manager
    function sendDeadlineEmail(taskId) {
        // Show modal để nhập message
        showMessageModal(taskId, true);
    }

    // Mark notification as read
    function markNotificationAsRead(notificationId) {
        fetch(`/api/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Reload notifications
                const notificationList = document.getElementById('notification-list');
                fetch('/api/notifications')
                    .then(res => res.json())
                    .then(result => {
                        displayNotifications(result.notifications.data);
                        updateBadge(result.unread_count);
                    });
            })
            .catch(error => console.error('Error:', error));
    }

    function displayNotifications(notifications) {
        const notificationList = document.getElementById('notification-list');
        if (notifications.length === 0) {
            notificationList.innerHTML = '<div class="p-4 text-center text-gray-500">Không có thông báo nào</div>';
            return;
        }

        notificationList.innerHTML = notifications.map(notif => `
        <div class="notification-item ${notif.read_at ? '' : 'unread'}">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800">${notif.data.message}</p>
                    <p class="text-xs text-gray-500 mt-1">${new Date(notif.created_at).toLocaleString('vi-VN')}</p>
                </div>
                ${!notif.read_at ? '<span class="inline-block w-2 h-2 bg-blue-600 rounded-full mt-1"></span>' : ''}
            </div>
            <div class="notification-action">
                <button class="send-email-btn" onclick="sendDeadlineEmail(${notif.data.task_id})">
                    <i class="fas fa-envelope mr-1"></i> Gửi email
                </button>
                <button class="mark-read-btn" onclick="markNotificationAsRead('${notif.id}')">
                    <i class="fas fa-check mr-1"></i> Đánh dấu
                </button>
            </div>
        </div>
    `).join('');
    }

    function updateBadge(count) {
        const notificationBadge = document.getElementById('notification-badge');
        if (count > 0) {
            notificationBadge.textContent = count;
            notificationBadge.classList.remove('hidden');
        } else {
            notificationBadge.classList.add('hidden');
        }
    }

    // Show message modal
    let currentTaskIdModal = null;

    function showMessageModal(taskId, fromNotification = false) {
        currentTaskIdModal = taskId;

        const message = prompt('Nhập nội dung thông báo cho quản lý (tối thiểu 5 ký tự):', 'Task này đã hết hạn. Vui lòng xem xét gia hạn hoặc hỗ trợ thêm.');
        if (message && message.trim().length >= 5) {
            sendDeadlineNotification(taskId, message, fromNotification);
        } else if (message) {
            alert('Nội dung phải tối thiểu 5 ký tự');
        }
    }

    function sendDeadlineNotification(taskId, message, fromNotification = false) {
        fetch(`/api/tasks/${taskId}/send-deadline-email`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    custom_message: message
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    if (fromNotification) {
                        loadNotifications();
                    }
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Lỗi khi gửi thông báo');
            });
    }
</script>
@endif