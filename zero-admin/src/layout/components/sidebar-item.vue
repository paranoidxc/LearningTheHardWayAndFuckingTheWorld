<template>
    <el-row class="tac">
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
                    <el-menu-item index="1-1">item one</el-menu-item>
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
    </el-row>
</template>



<script lang="ts" setup>
  import { getCurrentInstance, proxyRefs } from 'vue';
  const {proxy} = getCurrentInstance();
  
  async function handleClick() {
  }

import {
  Document,
  Menu as IconMenu,
  Location,
  Setting,
} from '@element-plus/icons-vue'

const handleSelect = (key: string, keyPath: string[]) => {
    console.log("select")
    console.log(key, keyPath)
}

const handleOpen = (key: string, keyPath: string[]) => {
    console.log("handleOpen")
  console.log(key, keyPath)
}

const handleClose = (key: string, keyPath: string[]) => {
    console.log("handleClose")
  console.log(key, keyPath)
}
  </script>