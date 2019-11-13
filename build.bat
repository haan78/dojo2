@echo off
IF EXIST dist (
    rmdir dist /s /q
)

npm run build