# Bismillahirrahmanirraheem
# Author: Dahir Muhammad Dahir
# Date: 17th-February-2021 11:11 AM
# About: this Dockerfile uses multi-stage build
# to create a small sized container for the grpc_fingerprint_server

FROM dahirmuhammaddahir/bexils_grpc:1.0.0 as stage1

WORKDIR /temp
ENV VERSION="v1.1.0"
# the order of the commands is
RUN apt update && apt install -y wget && \
    wget https://github.com/Bexils/grpc-fingerprint-engine/releases/download/${VERSION}/libdpfj.tar && \
    tar -xvf libdpfj.tar && \
    cp -r opt/* /opt && \
    cp lib/* /lib && \
    rm libdpfj.tar && \
    apt purge -y --auto-remove wget

WORKDIR /app
COPY src/ ./src
RUN cd src/cpp/build && cmake ../ && make -j

FROM ubuntu:20.04

ENV PORT=4134
ENV PATH=$PATH:/app
COPY --from=stage1 /opt /opt
COPY --from=stage1 /temp/lib /lib

WORKDIR /app
COPY --from=stage1 /app/src/cpp/build/fingerprint_server .

ENTRYPOINT [ "fingerprint_server" ]
