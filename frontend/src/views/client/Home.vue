<template>
    <LayoutDashboard>
        <div class="p-6 space-y-6">
            <Header />
            <div class="grid gap-6 lg:grid-cols-4">
                <!-- Left Column -->
                <div class="space-y-6">
                    <SupportPin />
                    <!-- User Info -->
                    <UserInfo />
                    <!-- Billing Summary -->
                    <BillingInfo />
                </div>
                <!-- Main Content -->
                <div class="lg:col-span-3 space-y-6">
                    <!-- Stats Grid -->
                    <Stats />
                    <!-- Active Products -->
                    <ProductList />
                    <!-- Recent Tickets -->
                    <TicketList />
                </div>
            </div>
        </div>
    </LayoutDashboard>
</template>

<script setup lang="ts">
import LayoutDashboard from '@/components/client/LayoutDashboard.vue';
import SupportPin from '@/components/client/Dashboard/Main/SupportPin.vue';
import ProductList from '@/components/client/Dashboard/Main/ProductList.vue';
import Stats from '@/components/client/Dashboard/Main/Stats.vue';
import BillingInfo from '@/components/client/Dashboard/Main/BillingInfo.vue';
import TicketList from '@/components/client/Dashboard/Main/TicketList.vue';
import UserInfo from '@/components/client/Dashboard/Main/UserInfo.vue';
import Header from '@/components/client/Dashboard/Main/Header.vue';
import { MythicalDOM } from '@/mythicalclient/MythicalDOM';

MythicalDOM.setPageTitle(MythicalDOM.getTranslation('dashboard.title'));
</script>
