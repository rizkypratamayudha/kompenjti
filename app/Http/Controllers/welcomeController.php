// namespace App\Http\Controllers;

// use Illuminate\Http\Request;

// class welcomeController extends Controller
// {
// public function index(){
// $breadcrumb = (object)[
// 'title'=>'Selamat Datang',
// 'list'=>['Home','Dashboard']
// ];


// $activeMenu = 'dashboard';

// return view('welcome', ['breadcrumb'=>$breadcrumb,'activeMenu'=>$activeMenu]);
// }

// public function mahasiswa(){
// $breadcrumb = (object)[
// 'title'=>'Selamat Datang',
// 'list'=>['Home','Dashboard Mahasiswa']
// ];

// $activeMenu = 'dashboardMhs';

// return view('mahasiswa.dashboard',['breadcrumb'=>$breadcrumb,'activeMenu'=>$activeMenu]);
// }
// public function dosen(){
// $breadcrumb = (object)[
// 'title'=>'Selamat Datang',
// 'list'=>['Home','Dashboard Dosen']
// ];

// $activeMenu = 'dashboardDos';

// return view('dosen.dashboard',['breadcrumb'=>$breadcrumb,'activeMenu'=>$activeMenu]);
// }
// public function kaprodi(){
// $breadcrumb = (object)[
// 'title'=>'Selamat Datang',
// 'list'=>['Home','Dashboard Kaprodi']
// ];

// $activeMenu = 'dashboardKap';

// return view('kaprodi.dashboard',['breadcrumb'=>$breadcrumb,'activeMenu'=>$activeMenu]);
// }

// public function contact(){
// $breadcrumb = (object)[
// 'title'=> 'Contact',
// 'list'=> ['Home','Contact'],
// ];

// $page = (object)[
// 'title'=> 'Contact',
// ];
// $activeMenu = 'dashboard';
// return view('contact',['activeMenu'=>$activeMenu,'breadcrumb'=>$breadcrumb,'page'=>$page]);
// }
// }