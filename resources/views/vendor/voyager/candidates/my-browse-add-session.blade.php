@php
    if (isset($_GET['id']) && isset($_GET['type']) && isset($_GET['name'])){
        session(['id' => $_GET['id']]);
        session(['type' => $_GET['type']]);
        session(['name' => $_GET['name']]);
    }
@endphp