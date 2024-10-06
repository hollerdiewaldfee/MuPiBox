import { CommonModule } from '@angular/common'
import { NgModule } from '@angular/core'
import { FormsModule } from '@angular/forms'

import { IonicModule } from '@ionic/angular'

import { AdminPageRoutingModule } from './admin-routing.module'

import { AdminPage } from './admin.page'

@NgModule({
    imports: [CommonModule, FormsModule, IonicModule, AdminPageRoutingModule, AdminPage],
})
export class AdminPageModule {}
