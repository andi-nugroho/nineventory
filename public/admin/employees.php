<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';

use Nineventory\Auth;
use Nineventory\Employee;

$auth = new Auth($pdo);
$auth->requireAdmin();

$employee = new Employee($pdo);
$currentUser = $auth->getCurrentUser();
$isAdmin = $auth->isAdmin();

$availableUsers = $employee->getAvailableUsers();

$message = '';
$error = '';


if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = $_GET['id'] ?? null;

    if ($action === 'delete' && $id) {
        $result = $employee->delete($id);
        if ($result['success']) {
            $message = $result['message'];
        } else {
            $error = $result['message'];
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $nama_karyawan = trim($_POST['nama_karyawan'] ?? '');
    $jabatan = trim($_POST['jabatan'] ?? '');
    $departemen = trim($_POST['departemen'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $user_id = !empty($_POST['user_id']) ? intval($_POST['user_id']) : null;

    if ($id) {
        $result = $employee->update($id, $nama_karyawan, $jabatan, $departemen, $email, $telepon, $user_id);
    } else {
        $result = $employee->create($nama_karyawan, $jabatan, $departemen, $email, $telepon, $user_id);
    }

    if ($result['success']) {
        $message = $result['message'];
        // Refresh the list of available users after a successful operation
        $availableUsers = $employee->getAvailableUsers();
    } else {
        $error = $result['message'];
    }
}


$employees = $employee->getAll();
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Employees - NINEVENTORY</title>
    <link rel="icon" href="../favicon.ico" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['DM Sans', 'sans-serif'],
                    },
                    colors: {
                        primary: '#FF6626', // Orange
                        secondary: '#1E293B', // Slate 800
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">


    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>


    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        [x-cloak] { display: none !important; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans text-slate-800 dark:text-white transition-colors duration-300"
      x-data="{
          darkMode: localStorage.getItem('theme') === 'dark',
          toggleTheme() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
              if (this.darkMode) {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          }
      }"
      x-init="$watch('darkMode', val => val ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark')); if(darkMode) document.documentElement.classList.add('dark');">

    <div class="flex h-screen overflow-hidden bg-gray-50 dark:bg-gray-950">


        <div class="fixed inset-0 -z-10 bg-gray-50 dark:bg-gray-950">
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-orange-500/10 blur-[100px] animate-pulse"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-red-500/10 blur-[100px] animate-pulse"></div>
        </div>

        <?php
        $activePage = 'employees';
        $pathPrefix = '../';
        include '../includes/sidebar.php';
        ?>


        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">

            <?php
            $pageTitle = 'Manage Employees';
            include '../includes/header.php';
            ?>

            <main class="flex-1 overflow-y-auto p-4 md:p-8 scroll-smooth hide-scrollbar">
                <div class="max-w-7xl mx-auto space-y-6">

                    <?php if ($message): ?>
                        <div class="p-4 rounded-2xl bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800 flex items-center gap-3">
                            <i data-lucide="check-circle" class="w-5 h-5"></i> <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="p-4 rounded-2xl bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800 flex items-center gap-3">
                            <i data-lucide="alert-circle" class="w-5 h-5"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>


                    <div x-data="{ open: false }">
                        <button @click="$dispatch('add-employee')" class="w-full md:w-auto px-4 py-2 bg-orange-600 text-white rounded-xl mb-4 flex items-center gap-2 hover:bg-orange-700 transition">
                            <i data-lucide="plus" class="w-4 h-4"></i> Add New Employee
                        </button>

                        <div x-show="open" x-transition.origin.top class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm mb-8"
                             x-data='{
                                editMode: false,
                                employee: { id: "", nama_karyawan: "", jabatan: "", departemen: "", email: "", telepon: "", user_id: "" }
                             }'
                             @edit-employee.window="
                                open = true;
                                editMode = true;
                                employee = $event.detail;
                                $nextTick(() => document.getElementById('form-title').textContent = 'Edit Employee');
                             "
                             @add-employee.window="
                                open = true;
                                editMode = false;
                                employee = { id: '', nama_karyawan: '', jabatan: '', departemen: '', email: '', telepon: '', user_id: '' };
                                $nextTick(() => document.getElementById('form-title').textContent = 'Add New Employee');
                             "
                        >
                            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                                <h3 id="form-title" class="text-lg font-bold text-gray-900 dark:text-white">Add New Employee</h3>
                            </div>
                            <div class="p-6">
                                <form method="POST" class="space-y-4">
                                    <input type="hidden" name="id" :value="employee.id">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                                            <input type="text" name="nama_karyawan" required :value="employee.nama_karyawan" class="w-full px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 dark:focus:ring-orange-500/40 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Position</label>
                                            <input type="text" name="jabatan" :value="employee.jabatan" class="w-full px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 dark:focus:ring-orange-500/40 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department</label>
                                            <input type="text" name="departemen" :value="employee.departemen" class="w-full px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 dark:focus:ring-orange-500/40 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                                            <input type="email" name="email" :value="employee.email" class="w-full px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 dark:focus:ring-orange-500/40 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                                            <input type="text" name="telepon" :value="employee.telepon" class="w-full px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 dark:focus:ring-orange-500/40 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Link to User Account</label>
                                            <select name="user_id" class="w-full px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 dark:focus:ring-orange-500/40 outline-none">
                                                <option value="">-- Not Linked --</option>
                                                
                                                <template x-if="editMode && employee.user_id">
                                                     <option :value="employee.user_id" selected x-text="employee.username"></option>
                                                </template>

                                                <?php foreach ($availableUsers as $user): ?>
                                                    <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <button type="submit" class="px-6 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-xl hover:opacity-90 transition">Save Employee</button>
                                </form>
                            </div>
                        </div>
                    </div>


                    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Employee List</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                                    <tr>
                                        <th class="px-6 py-4 font-semibold">Name</th>
                                        <th class="px-6 py-4 font-semibold">Position</th>
                                        <th class="px-6 py-4 font-semibold">Contact</th>
                                        <th class="px-6 py-4 font-semibold">Linked User</th>
                                        <th class="px-6 py-4 font-semibold text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <?php foreach ($employees as $emp): ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($emp['nama_karyawan']) ?></div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($emp['departemen']) ?></div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($emp['jabatan']) ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            <?php if($emp['email']): ?><div class="flex items-center gap-2"><i data-lucide="mail" class="w-3 h-3"></i> <?= htmlspecialchars($emp['email']) ?></div><?php endif; ?>
                                            <?php if($emp['telepon']): ?><div class="flex items-center gap-2"><i data-lucide="phone" class="w-3 h-3"></i> <?= htmlspecialchars($emp['telepon']) ?></div><?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <?php if ($emp['username']): ?>
                                                <span class="px-2 py-1 rounded-lg bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400 text-xs font-medium flex items-center gap-1.5 w-fit">
                                                    <i data-lucide="user-check" class="w-3 h-3"></i>
                                                    <?= htmlspecialchars($emp['username']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-gray-400 text-xs">Not Linked</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button @click="$dispatch('edit-employee', <?= htmlspecialchars(json_encode($emp)) ?>)" class="p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
                                                <i data-lucide="edit" class="w-4 h-4"></i>
                                            </button>
                                            <a href="?action=delete&id=<?= $emp['id'] ?>" onclick="return confirm('Delete this employee? This action cannot be undone.')" class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    
    <script>lucide.createIcons();</script>

    <?php include '../includes/chatbot-widget.php'; ?>
    <script src="../assets/js/chatbot.js"></script>
</body>
</html>
