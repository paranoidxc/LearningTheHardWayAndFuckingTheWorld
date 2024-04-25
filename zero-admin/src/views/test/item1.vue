<template>
    <base-wrapper class="bg-color-primary flex-center-center">

        <div class="flex-c-center-center bg-color-white b-rd-10" style="height: 400px; width: 500px">
            <h1 class="font-size-lg">4444</h1>

        </div>
    </base-wrapper>
</template>

<script setup>
  import { getCurrentInstance, proxyRefs } from 'vue';
  // 组件实例
  const { proxy } = getCurrentInstance();
  </script>

<style lang="scss" scoped>
.copyright {
    width: 100%;
    position: absolute;
    bottom: 0;
    font-size: 12px;
    text-align: center;
    color: #ccc;
}
</style>