<template>
  <!--
  <el-menu router :default-active="$route.meta.fullPath" :collapse="false" :unique-opened="false" @select="handleSelect">
    <el-scrollbar>
      <sidebar-item :router-list="routerList" />
    </el-scrollbar>
  </el-menu>
  -->
  <el-menu 
        router
        default-active="2" 
        class="el-menu-vertical-demo" 
        @select="handleSelect"
        @open="handleOpen" 
        @close="handleClose">
            <el-sub-menu index="1">
                <template #title>
                    <el-icon>
                        <location />
                    </el-icon>
                    <span>Navigator One</span>
                </template>
                <el-menu-item-group title="Group One">
                    <el-menu-item index="1-1" route="/item1">item one</el-menu-item>
                    <el-menu-item index="1-2">item two</el-menu-item>
                </el-menu-item-group>
                <el-menu-item-group title="Group Two">
                    <el-menu-item index="1-3">item three</el-menu-item>
                </el-menu-item-group>
                <el-sub-menu index="1-4">
                    <template #title>item four</template>
                    <el-menu-item index="1-4-1">item one</el-menu-item>
                </el-sub-menu>
            </el-sub-menu>
            <el-menu-item index="2" route="/test">
                <el-icon><icon-menu /></el-icon>
                <span>Navigator Two</span>
            </el-menu-item>
            <el-menu-item index="3" disabled>
                <el-icon>
                    <document />
                </el-icon>
                <span>Navigator Three</span>
            </el-menu-item>
            <el-menu-item index="4">
                <el-icon>
                    <setting />
                </el-icon>
                <span>Navigator Four</span>
            </el-menu-item>
        </el-menu>
</template>

<script setup>
import sidebarItem from './sidebar-item.vue';
import { getCurrentInstance, toRefs } from 'vue';
const { proxy } = getCurrentInstance();
let { routerList, routerMap } = toRefs(proxy.$store.user.useUserStore());
let { activeTabs } = proxy.$store.settings.useSettingsStore();

/**
 * 选中菜单时触发
 * @param index 选中菜单项的 index  eg: /system/role （router 以 index 作为 path 进行路由跳转，或 router 属性直接跳转）
 * @param indexPath 选中菜单项的 index path eg: ['/system', '/system/role']
 * @param item 选中菜单项
 * @param routeResult vue-router 的返回值（如果 router 为 true）
 */
function handleSelect(index, indexPath, item, routeResult) {
  // console.log(index, indexPath, item, routeResult);
  // proxy.$router.push(index);
  activeTabs(routerMap.value[index]);
}
</script>

<style lang="scss" scoped>
.el-menu {
  box-shadow: 1px 0 5px rgba(0, 0, 0, 0.2);
}
</style>
