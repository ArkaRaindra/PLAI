<?php

return [
    'tree' => [
        'empty_label' => 'Tidak ada data',
    ],

    'action' => [
        'create_child_node' => 'Tambah Sub-standar',

        'delete_failed_title' => 'Gagal menghapus',
        'delete_failed_body_has_child' => 'Node masih memiliki sub-standar. Hapus sub-standar terlebih dahulu.',

        'move_node' => 'Pindahkan node',
        'move_node_success' => 'Node berhasil dipindahkan',
        'move_node_failed' => 'Gagal memindahkan node',
        'move_node_failed_body_depth' => 'Gagal memindahkan node, level target tidak boleh melebihi level maksimum :level.',

        'fix_nestedset' => 'Perbaiki tree',
        'fix_nestedset_success' => 'Tree berhasil diperbaiki',
    ],

    'field' => [
        'parent_select_field' => 'Induk Standar',
        'parent_select_field_placeholder' => 'Pilih induk standar',
        'parent_select_field_empty_label' => 'Tidak ada induk standar',
    ],
];
