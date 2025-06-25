<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions grouped by module
$permissions = [
    // Clients
    'voirClients',
    'creerClients',
    'exporterClients',
    'modifierClients',
    'supprimerClients',

    // Ventes
    'voirVentes',
    'creerVentes',
    'marquerPaye',
    'modifierVentes',
    'supprimerVentes',
    'genererFacture',

    // Stocks
    'voirStocks',
    'creerStocks',
    'modifierStocks',
    'supprimerStocks',

    // Utilisateurs
    'voirUtilisateurs',
    'creerUtilisateurs',
    'modifierUtilisateurs',
    'supprimerUtilisateurs',
    'exporterUtilisateurs',

    // Journal
    'voirJournal',

    // Produits
    'voirProduits',
    'creerProduits',
    'exporterProduits',
    'modifierProduits',
    'supprimerProduits',

    // Catégories
    'voirCategories',
    'creerCategories',
    'modifierCategories',
    'supprimerCategories',

    // Notifications
    'voirNotifications'
];



        // Create permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create admin role and assign all permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo($permissions);

        // Create manager role with limited permissions
        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->givePermissionTo([
            'voirProduits', 'creerProduits', 'modifierProduits', 'supprimerProduits',
            
        ]);

        // Create cashier role with basic permissions
        $cashierRole = Role::create(['name' => 'cashier']);
        $cashierRole->givePermissionTo([
            'voirProduits',
            
        ]);
    }
}
